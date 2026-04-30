<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionPaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TransactionPayment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $transactions = Transaction::all();
        $users = User::pluck('id')->toArray();
        $adminUserId = !empty($users) ? $users[array_rand($users)] : null;

        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI'];

        foreach ($transactions as $transaction) {
            $grandTotal = $transaction->grand_total;
            if ($grandTotal <= 0) continue;

            // Randomly decide payment scenario: unpaid (0), deposit (1), paid (2)
            $scenario = rand(0, 2);

            $totalPaid = 0;
            $paymentStatus = 'unpaid';

            if ($scenario === 1) { // Deposit (partial payment)
                $amount = rand(50000, max(50000, intval($grandTotal * 0.5)));
                $totalPaid = $amount;
                $paymentStatus = 'deposit';
                
                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'payment_date' => now()->subDays(rand(1, 10)),
                    'bank_name' => $banks[array_rand($banks)],
                    'account_number' => '123' . rand(1000000, 9999999),
                    'created_by' => $adminUserId,
                    'amount' => $amount,
                ]);
            } elseif ($scenario === 2) { // Paid (can be multiple payments)
                $numPayments = rand(1, 3);
                $remaining = $grandTotal;

                for ($i = 0; $i < $numPayments; $i++) {
                    if ($i === $numPayments - 1) {
                        $amount = $remaining; // Last payment pays the rest
                    } else {
                        $amount = rand(50000, max(50000, intval($remaining * 0.5)));
                        $remaining -= $amount;
                    }

                    TransactionPayment::create([
                        'transaction_id' => $transaction->id,
                        'payment_date' => now()->subDays(rand(1, 10)),
                        'bank_name' => $banks[array_rand($banks)],
                        'account_number' => '123' . rand(1000000, 9999999),
                        'created_by' => $adminUserId,
                        'amount' => $amount,
                    ]);
                }
                
                $totalPaid = $grandTotal;
                $paymentStatus = 'paid';
            }

            // Update transaction payment_status
            $transaction->payment_status = $paymentStatus;
            
            // Automation rule if direct_order
            if ($transaction->transaction_type === 'direct_order') {
                if ($paymentStatus === 'paid' && $transaction->item_status === 'collected') {
                    $transaction->status = 'done';
                } else {
                    $transaction->status = 'on progress';
                }
            } else {
                // pre_order
                $transaction->status = 'on progress';
                $transaction->item_status = 'in_progress';
            }

            $transaction->save();
        }
    }
}
