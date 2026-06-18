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

            $timezone = $data['device_timezone'] ?? config('app.timezone');
            $localTime = \Carbon\Carbon::now($timezone);
            $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localTime->toDateTimeString(), config('app.timezone'));

            // Bila tanggal bayar dipilih dari UI, parse menggunakan timezone klien agar jam-menitnya tepat
            if (isset($data['payment_date'])) {
                $localPaymentDate = \Carbon\Carbon::parse($data['payment_date'], $timezone);
                $paymentDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localPaymentDate->toDateTimeString(), config('app.timezone'));
            } else {
                $paymentDate = $now;
            }

            $transferToAccount = \App\Models\Account::find($data['transfer_to_id']);
            $bankName = $transferToAccount ? $transferToAccount->name : ($data['bank_name'] ?? '-');

            $payment = new TransactionPayment([
                'transaction_id' => $transaction->id,
                'payment_date' => $paymentDate,
                'bank_name' => $bankName,
                'account_number' => $data['account_number'] ?? null,
                'amount' => $data['amount'],
                'created_by' => $userId ?? auth()->id(),
            ]);
            $payment->created_at = $now;
            $payment->updated_at = $now;
            $payment->save();

            $cashTx = new \App\Models\CashTransaction([
                'date' => $payment->payment_date,
                'description' => 'Pembayaran transaksi - ' . $transaction->trx_id,
                'type' => 'money_in',
                'amount' => $payment->amount,
                'client_id' => $transaction->client_id,
                'account_id' => $data['transfer_to_id'],
                'counter_account_id' => $data['category_id'],
                'reference_type' => 'transaction',
                'reference_id' => $transaction->trx_id,
            ]);
            $cashTx->created_at = $now;
            $cashTx->updated_at = $now;
            $cashTx->save();

            $cashTx->generateJournal();

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

            $transaction->updated_at = $now;
            $transaction->save();

            $log = new \App\Models\TransactionLog([
                'transaction_id' => $transaction->id,
                'user_id' => $userId ?? auth()->id(),
                'action' => 'Updated payment',
            ]);
            $log->created_at = $now;
            $log->updated_at = $now;
            $log->save();

            return $payment;
        });
    }
}
