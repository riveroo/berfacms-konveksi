<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\TransactionPayment;
use App\Models\User;

class TransactionEnhancementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::all();
        $admin = User::first();

        foreach ($transactions as $transaction) {
            // Seed transaction_logs
            if ($transaction->logs()->count() === 0) {
                TransactionLog::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $admin ? $admin->id : null,
                    'action' => 'Created transaction',
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->created_at,
                ]);

                if ($transaction->payment_status !== 'unpaid') {
                    TransactionLog::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $admin ? $admin->id : null,
                        'action' => 'Updated payment',
                        'created_at' => $transaction->updated_at,
                        'updated_at' => $transaction->updated_at,
                    ]);
                }
            }

            // transaction_payments was seeded in TransactionPaymentSeeder, 
            // but if there are transactions without payments and status is not unpaid:
            if ($transaction->payment_status !== 'unpaid' && $transaction->payments()->count() === 0) {
                $amount = $transaction->payment_status === 'paid' 
                            ? $transaction->grand_total 
                            : round($transaction->grand_total / 2);

                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'payment_date' => $transaction->updated_at,
                    'bank_name' => 'BCA',
                    'account_number' => '1234567890',
                    'amount' => $amount,
                    'created_by' => $admin ? $admin->id : null,
                ]);
            }
        }
    }
}
