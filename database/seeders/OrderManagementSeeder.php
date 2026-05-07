<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Transaction;

class OrderManagementSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['client_name' => 'Budi Santoso', 'type' => 'customer', 'email' => 'budi@example.com', 'phone_number' => '081234567890', 'information' => 'Pelanggan tetap'],
            ['client_name' => 'Siti Rahayu', 'type' => 'customer', 'email' => 'siti@example.com', 'phone_number' => '082345678901', 'information' => null],
            ['client_name' => 'Ahmad Fauzi', 'type' => 'customer', 'email' => 'ahmad@example.com', 'phone_number' => '083456789012', 'information' => 'Langganan setiap bulan'],
            ['client_name' => 'Dewi Kusuma', 'type' => 'customer', 'email' => 'dewi@example.com', 'phone_number' => '084567890123', 'information' => null],
            ['client_name' => 'Rizky Pratama', 'type' => 'customer', 'email' => 'rizky@example.com', 'phone_number' => '085678901234', 'information' => 'Reseller'],
            ['client_name' => 'Megah Konveksi', 'type' => 'supplier', 'email' => 'megah@supplier.com', 'phone_number' => '086789012345', 'information' => 'Supplier Kain'],
            ['client_name' => 'Hendra Wijaya', 'type' => 'customer', 'email' => 'hendra@example.com', 'phone_number' => '087890123456', 'information' => 'VIP Client'],
            ['client_name' => 'Novita Sari', 'type' => 'customer', 'email' => 'novita@example.com', 'phone_number' => '088901234567', 'information' => null],
            ['client_name' => 'Salim Textiles', 'type' => 'vendor', 'email' => 'salim@vendor.com', 'phone_number' => '089012345678', 'information' => 'Vendor Kancing'],
            ['client_name' => 'Rini Susanti', 'type' => 'customer', 'email' => 'rini@example.com', 'phone_number' => '089123456789', 'information' => null],
        ];

        foreach ($clients as $data) {
            Client::updateOrCreate(['phone_number' => $data['phone_number']], $data);
        }

        $statuses = Transaction::STATUSES;
        $clientIds = Client::pluck('id')->toArray();
        $transactionTypes = ['pre_order', 'direct_order'];
        $itemStatuses = ['in_progress', 'awaiting_pickup', 'collected'];
        $paymentStatuses = ['unpaid', 'deposit', 'paid'];

        for ($i = 1; $i <= 10; $i++) {
            Transaction::create([
                'trx_id'      => 'TRX-' . strtoupper(uniqid()),
                'client_id'   => $clientIds[array_rand($clientIds)],
                'total_price' => rand(150000, 5000000),
                'status'      => $statuses[array_rand($statuses)],
                'transaction_type' => $transactionTypes[array_rand($transactionTypes)],
                'item_status' => $itemStatuses[array_rand($itemStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
            ]);
        }
    }
}
