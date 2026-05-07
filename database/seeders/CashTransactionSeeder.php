<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Client;
use Illuminate\Database\Seeder;

class CashTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashAccount = Account::where('code', '1001')->first();
        $revenueAccount = Account::where('code', '4001')->first();
        $expenseAccount = Account::where('code', '5002')->first();
        
        $clients = Client::all();
        $customer = $clients->where('type', 'customer')->first();
        $supplier = $clients->where('type', 'supplier')->first();

        if ($cashAccount && $revenueAccount) {
            // Money In from Customer
            CashTransaction::create([
                'date' => now(),
                'description' => 'Payment from ' . ($customer->client_name ?? 'Customer'),
                'type' => 'in',
                'amount' => 1000000.00,
                'account_id' => $cashAccount->id,
                'counter_account_id' => $revenueAccount->id,
                'client_id' => $customer->id ?? null,
                'reference_type' => 'manual',
            ]);
        }

        if ($cashAccount && $expenseAccount) {
            // Money Out to Supplier
            CashTransaction::create([
                'date' => now(),
                'description' => 'Payment for materials to ' . ($supplier->client_name ?? 'Supplier'),
                'type' => 'out',
                'amount' => 500000.00,
                'account_id' => $cashAccount->id,
                'counter_account_id' => $expenseAccount->id,
                'client_id' => $supplier->id ?? null,
                'reference_type' => 'manual',
            ]);
        }

        if ($cashAccount && $revenueAccount) {
            // Manual entry (receive_from only)
            CashTransaction::create([
                'date' => now(),
                'description' => 'Project Deposit',
                'type' => 'in',
                'amount' => 2500000.00,
                'account_id' => $cashAccount->id,
                'counter_account_id' => $revenueAccount->id,
                'receive_from' => 'Mr. Anonymous',
                'reference_type' => 'manual',
            ]);
        }

        if ($cashAccount && $expenseAccount) {
            // Both client_id and receive_from
            CashTransaction::create([
                'date' => now(),
                'description' => 'Office Rent',
                'type' => 'out',
                'amount' => 1200000.00,
                'account_id' => $cashAccount->id,
                'counter_account_id' => $expenseAccount->id,
                'client_id' => $clients->last()->id ?? null,
                'receive_from' => 'Building Management',
                'reference_type' => 'manual',
            ]);
        }
    }
}
