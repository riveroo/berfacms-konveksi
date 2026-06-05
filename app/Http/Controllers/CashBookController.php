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

        // Summary calculations
        $totalIn = 0;
        $totalOut = 0;

        if ($request->has('account_id') && $request->account_id) {
            $accId = $request->account_id;
            
            // Money In to this account:
            // 1. type = money_in and account_id = $accId
            // 2. type = transfer and account_id = $accId (destination)
            $totalIn = CashTransaction::where(function($q) use ($accId) {
                $q->whereIn('type', ['money_in', 'in'])->where('account_id', $accId);
            })->orWhere(function($q) use ($accId) {
                $q->where('type', 'transfer')->where('account_id', $accId);
            })->sum('amount');

            // Money Out from this account:
            // 1. type = money_out and account_id = $accId
            // 2. type = transfer and counter_account_id = $accId (source)
            $totalOut = CashTransaction::where(function($q) use ($accId) {
                $q->whereIn('type', ['money_out', 'out'])->where('account_id', $accId);
            })->orWhere(function($q) use ($accId) {
                $q->where('type', 'transfer')->where('counter_account_id', $accId);
            })->sum('amount');
        } else {
            // General overview (overall cashflow sum)
            $totalIn = CashTransaction::whereIn('type', ['money_in', 'in'])->sum('amount');
            $totalOut = CashTransaction::whereIn('type', ['money_out', 'out'])->sum('amount');
        }

        $balance = $totalIn - $totalOut;

        $accounts = Account::whereIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();

        return view('cash-book.index', compact('transactions', 'accounts', 'totalIn', 'totalOut', 'balance'));
    }

    public function create()
    {
        $accounts = Account::where('type', 'asset')->where('is_active', true)->get();
        $categories = Account::where('is_active', true)->get();
        $clients = Client::orderBy('client_name')->get();

        return view('cash-book.create', compact('accounts', 'categories', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'type' => 'required|in:money_in,money_out,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:account_id',
            'client_id' => 'nullable|exists:clients,id',
            'receive_from' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $tx = CashTransaction::create($request->all());
            $this->generateJournal($tx);

            return redirect()->route('cash-book.index')->with('success', 'Transaction saved.');
        });
    }

    public function show(CashTransaction $cashBook)
    {
        $cashBook->load(['journalEntry.details.account', 'client']);
        return view('cash-book.show', compact('cashBook'));
    }

    public function edit(CashTransaction $cashBook)
    {
        if ($cashBook->type === 'transfer') {
            return back()->with('error', 'Transfers cannot be edited directly yet. Delete and recreate.');
        }

        $accounts = Account::where('type', 'asset')->where('is_active', true)->get();
        $categories = Account::whereNotIn('subtype', ['cash', 'bank'])->where('is_active', true)->get();
        $clients = Client::orderBy('client_name')->get();

        return view('cash-book.edit', compact('cashBook', 'accounts', 'categories', 'clients'));
    }

    public function update(Request $request, CashTransaction $cashBook)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'type' => 'required|in:money_in,money_out,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:account_id',
            'client_id' => 'nullable|exists:clients,id',
            'receive_from' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $cashBook) {
            $cashBook->update($request->all());

            // Delete old journal
            if ($cashBook->journalEntry) {
                $cashBook->journalEntry()->delete();
            }

            // Generate new journal
            $this->generateJournal($cashBook);

            return redirect()->route('cash-book.index')->with('success', 'Transaction updated.');
        });
    }

    public function destroy(CashTransaction $cashBook)
    {
        return DB::transaction(function () use ($cashBook) {
            if ($cashBook->journalEntry) {
                $cashBook->journalEntry()->delete();
            }

            $cashBook->delete();

            return redirect()->route('cash-book.index')->with('success', 'Transaction deleted.');
        });
    }

    private function generateJournal(CashTransaction $tx)
    {
        $tx->generateJournal();
    }
}
