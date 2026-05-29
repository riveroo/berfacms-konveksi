<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TransactionInvoiceGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::create([
            'client_name' => 'John Doe',
            'phone_number' => '08123456789',
        ]);
    }

    public function test_transaction_generates_invoice_number_in_new_format(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 29, 12, 0, 0));

        $transaction = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 100000,
        ]);

        $this->assertEquals('INV0526-00001', $transaction->trx_id);

        Carbon::setTestNow();
    }

    public function test_invoice_numbers_increment_sequentially_within_same_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 29, 12, 0, 0));

        $t1 = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 100000,
        ]);
        $t2 = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 200000,
        ]);
        $t3 = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 300000,
        ]);

        $this->assertEquals('INV0526-00001', $t1->trx_id);
        $this->assertEquals('INV0526-00002', $t2->trx_id);
        $this->assertEquals('INV0526-00003', $t3->trx_id);

        Carbon::setTestNow();
    }

    public function test_invoice_number_resets_when_month_changes(): void
    {
        // May 2026
        Carbon::setTestNow(Carbon::create(2026, 5, 29, 12, 0, 0));

        $t1 = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 100000,
        ]);
        $this->assertEquals('INV0526-00001', $t1->trx_id);

        // Move to June 2026
        Carbon::setTestNow(Carbon::create(2026, 6, 1, 12, 0, 0));

        $t2 = Transaction::create([
            'client_id' => $this->client->id,
            'total_price' => 200000,
        ]);
        $this->assertEquals('INV0626-00001', $t2->trx_id);

        Carbon::setTestNow();
    }
}
