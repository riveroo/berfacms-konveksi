<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function createPayment(Transaction $transaction, array $data, $userId = null)
    {
        return DB::transaction(function () use ($transaction, $data, $userId) {
            $totalPaid = $transaction->payments()->sum('amount');
            $grandTotal = $transaction->grand_total;

            if ($totalPaid + $data['amount'] > $grandTotal) {
                throw ValidationException::withMessages([
                    'amount' => ['Payment exceeds total amount']
                ]);
            }

            $payment = TransactionPayment::create([
                'transaction_id' => $transaction->id,
                'payment_date' => $data['payment_date'] ?? now(),
                'bank_name' => $data['bank_name'],
                'account_number' => $data['account_number'],
                'amount' => $data['amount'],
                'created_by' => $userId ?? auth()->id(),
            ]);

            $newTotalPaid = $totalPaid + $data['amount'];
            
            if ($newTotalPaid == 0) {
                $paymentStatus = 'unpaid';
            } elseif ($newTotalPaid < $grandTotal) {
                $paymentStatus = 'deposit';
            } else {
                $paymentStatus = 'paid';
            }

            $transaction->payment_status = $paymentStatus;

            // Automation rule: if direct_order, paid, and collected -> done
            if ($transaction->transaction_type === 'direct_order' 
                && $paymentStatus === 'paid' 
                && $transaction->item_status === 'collected') {
                $transaction->status = 'done';
            }

            $transaction->save();

            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => $userId ?? auth()->id(),
                'action' => 'Updated payment',
            ]);

            return $payment;
        });
    }
}
