<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\TransactionTemplate;
use App\Models\TransactionTemplateLine;
use Illuminate\Database\Seeder;

class TransactionTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. SALES CASH
        $salesCash = TransactionTemplate::create([
            'code' => 'sales_cash',
            'name' => 'Sales (Cash)',
            'is_active' => true,
            'version' => 1,
        ]);

        TransactionTemplateLine::create([
            'template_id' => $salesCash->id,
            'account_id' => Account::where('code', '1001')->first()->id, // Cash
            'position' => 'debit',
            'amount_source' => 'input',
        ]);

        TransactionTemplateLine::create([
            'template_id' => $salesCash->id,
            'account_id' => Account::where('code', '4001')->first()->id, // Revenue
            'position' => 'credit',
            'amount_source' => 'input',
        ]);

        // 2. PURCHASE (CASH)
        $purchaseCash = TransactionTemplate::create([
            'code' => 'purchase_cash',
            'name' => 'Purchase (Cash)',
            'is_active' => true,
            'version' => 1,
        ]);

        TransactionTemplateLine::create([
            'template_id' => $purchaseCash->id,
            'account_id' => Account::where('code', '1003')->first()->id, // Inventory
            'position' => 'debit',
            'amount_source' => 'input',
        ]);

        TransactionTemplateLine::create([
            'template_id' => $purchaseCash->id,
            'account_id' => Account::where('code', '1001')->first()->id, // Cash
            'position' => 'credit',
            'amount_source' => 'input',
        ]);

        // 3. EXPENSE
        $expense = TransactionTemplate::create([
            'code' => 'expense',
            'name' => 'Operational Expense',
            'is_active' => true,
            'version' => 1,
        ]);

        TransactionTemplateLine::create([
            'template_id' => $expense->id,
            'account_id' => Account::where('code', '5002')->first()->id, // Operational Expense
            'position' => 'debit',
            'amount_source' => 'input',
        ]);

        TransactionTemplateLine::create([
            'template_id' => $expense->id,
            'account_id' => Account::where('code', '1001')->first()->id, // Cash
            'position' => 'credit',
            'amount_source' => 'input',
        ]);
    }
}
