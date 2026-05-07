<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Client;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with(['account', 'counterAccount', 'client'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->has('account_id') && $request->account_id) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(15);

        // Summary
        $summaryQuery = CashTransaction::query();
        if ($request->has('account_id') && $request->account_id) {
            $summaryQuery->where('account_id', $request->account_id);
        }
        
        $totalIn = (clone $summaryQuery)->where('type', 'in')->sum('amount');
        $totalOut = (clone $summaryQuery)->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        $accounts = Account::whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();

        return view('cash-book.index', compact('transactions', 'accounts', 'totalIn', 'totalOut', 'balance'));
    }

    public function create()
    {
        $accounts = Account::whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();
        $categories = Account::whereNotIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();
        $clients = Client::orderBy('client_name')->get();

        return view('cash-book.create', compact('accounts', 'categories', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'type' => 'required|in:in,out,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:account_id',
            'client_id' => 'nullable|exists:clients,id',
            'receive_from' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Transfer logic: create OUT from source, IN to destination
            if ($request->type === 'transfer') {
                // Out from Source
                $txOut = CashTransaction::create([
                    'date' => $request->date,
                    'description' => $request->description . ' (Transfer Out)',
                    'type' => 'out',
                    'amount' => $request->amount,
                    'account_id' => $request->account_id,
                    'counter_account_id' => $request->counter_account_id,
                    'reference_type' => 'transfer',
                    'client_id' => $request->client_id,
                    'receive_from' => $request->receive_from,
                ]);

                // In to Destination
                $txIn = CashTransaction::create([
                    'date' => $request->date,
                    'description' => $request->description . ' (Transfer In)',
                    'type' => 'in',
                    'amount' => $request->amount,
                    'account_id' => $request->counter_account_id, // destination
                    'counter_account_id' => $request->account_id, // source
                    'reference_type' => 'transfer',
                    'reference_id' => $txOut->id,
                    'client_id' => $request->client_id,
                    'receive_from' => $request->receive_from,
                ]);
                $txOut->update(['reference_id' => $txIn->id]);

                $this->generateJournal($txOut);
                
            } else {
                $tx = CashTransaction::create($request->all());
                $this->generateJournal($tx);
            }

            DB::commit();
            return redirect()->route('cash-book.index')->with('success', 'Transaction saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(CashTransaction $cashBook)
    {
        $cashBook->load(['journalEntry.details.account', 'client']);
        return view('cash-book.show', compact('cashBook'));
    }

    public function edit(CashTransaction $cashBook)
    {
        if ($cashBook->reference_type === 'transfer') {
            return back()->with('error', 'Transfers cannot be edited directly yet. Delete and recreate.');
        }

        $accounts = Account::whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();
        $categories = Account::whereNotIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();
        $clients = Client::orderBy('client_name')->get();

        return view('cash-book.edit', compact('cashBook', 'accounts', 'categories', 'clients'));
    }

    public function update(Request $request, CashTransaction $cashBook)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:account_id',
            'client_id' => 'nullable|exists:clients,id',
            'receive_from' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $cashBook->update($request->all());

            // Delete old journal
            if ($cashBook->journalEntry) {
                $cashBook->journalEntry()->delete();
            }

            // Generate new journal
            $this->generateJournal($cashBook);

            DB::commit();
            return redirect()->route('cash-book.index')->with('success', 'Transaction updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(CashTransaction $cashBook)
    {
        DB::beginTransaction();
        try {
            if ($cashBook->reference_type === 'transfer' && $cashBook->reference_id) {
                CashTransaction::where('id', $cashBook->reference_id)->delete();
            }

            if ($cashBook->journalEntry) {
                $cashBook->journalEntry()->delete();
            }

            $cashBook->delete();

            DB::commit();
            return redirect()->route('cash-book.index')->with('success', 'Transaction deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function generateJournal(CashTransaction $tx)
    {
        $journal = JournalEntry::create([
            'date' => $tx->date,
            'description' => $tx->description,
            'reference_type' => 'cash_transaction',
            'reference_id' => $tx->id,
        ]);

        if ($tx->type === 'in') {
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $tx->account_id,
                'debit' => $tx->amount,
                'credit' => 0,
            ]);
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $tx->counter_account_id,
                'debit' => 0,
                'credit' => $tx->amount,
            ]);
        } else {
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $tx->counter_account_id,
                'debit' => $tx->amount,
                'credit' => 0,
            ]);
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $tx->account_id,
                'debit' => 0,
                'credit' => $tx->amount,
            ]);
        }
    }
}
