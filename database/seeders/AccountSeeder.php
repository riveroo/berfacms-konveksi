<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coa = [
            // ASSETS (1000)
            ['code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1001', 'name' => 'Cash', 'type' => 'asset', 'parent_code' => '1000'],
            ['code' => '1002', 'name' => 'Bank', 'type' => 'asset', 'parent_code' => '1000'],
            ['code' => '1003', 'name' => 'Inventory', 'type' => 'asset', 'parent_code' => '1000'],

            // LIABILITY (2000)
            ['code' => '2000', 'name' => 'Liabilities', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2001', 'name' => 'Accounts Payable', 'type' => 'liability', 'parent_code' => '2000'],

            // EQUITY (3000)
            ['code' => '3000', 'name' => 'Equity', 'type' => 'equity', 'parent_id' => null],
            ['code' => '3001', 'name' => 'Owner Capital', 'type' => 'equity', 'parent_code' => '3000'],

            // REVENUE (4000)
            ['code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'parent_id' => null],
            ['code' => '4001', 'name' => 'Sales Revenue', 'type' => 'revenue', 'parent_code' => '4000'],

            // EXPENSE (5000)
            ['code' => '5000', 'name' => 'Expenses', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5001', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'parent_code' => '5000'],
            ['code' => '5002', 'name' => 'Operational Expense', 'type' => 'expense', 'parent_code' => '5000'],
        ];

        foreach ($coa as $item) {
            $parentId = null;
            if (isset($item['parent_code'])) {
                $parent = Account::where('code', $item['parent_code'])->first();
                $parentId = $parent ? $parent->id : null;
            }

            Account::create([
                'code' => $item['code'],
                'name' => $item['name'],
                'type' => $item['type'],
                'parent_id' => $parentId,
                'is_active' => true,
            ]);
        }
    }
}
