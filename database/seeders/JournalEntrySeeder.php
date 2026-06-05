<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use App\Models\Account;
use Carbon\Carbon;

class JournalEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch accounts
        $cash = Account::where('code', '1001')->first();
        $bank = Account::where('code', '1002')->first();
        $inventory = Account::where('code', '1003')->first();
        $payable = Account::where('code', '2001')->first();
        $capital = Account::where('code', '3001')->first();
        $sales = Account::where('code', '4001')->first();
        $cogs = Account::where('code', '5001')->first();
        $operational = Account::where('code', '5002')->first();

        if (!$cash || !$bank || !$inventory || !$payable || !$capital || !$sales || !$cogs || !$operational) {
            $this->command->error('COA accounts not fully set up. Run AccountSeeder first.');
            return;
        }

        // 30 Transactions definition
        $transactions = [
            [
                'desc' => 'Setoran modal awal pemilik',
                'date' => Carbon::now()->subDays(60),
                'details' => [
                    ['account' => $bank, 'debit' => 100000000.0, 'credit' => 0.0],
                    ['account' => $capital, 'debit' => 0.0, 'credit' => 100000000.0],
                ]
            ],
            [
                'desc' => 'Tarik tunai dari bank ke kas kecil',
                'date' => Carbon::now()->subDays(59),
                'details' => [
                    ['account' => $cash, 'debit' => 5000000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 5000000.0],
                ]
            ],
            [
                'desc' => 'Pembelian bahan baku kain/benang tunai',
                'date' => Carbon::now()->subDays(58),
                'details' => [
                    ['account' => $inventory, 'debit' => 12000000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 12000000.0],
                ]
            ],
            [
                'desc' => 'Pembelian bahan penunjang kancing kredit',
                'date' => Carbon::now()->subDays(56),
                'details' => [
                    ['account' => $inventory, 'debit' => 4500000.0, 'credit' => 0.0],
                    ['account' => $payable, 'debit' => 0.0, 'credit' => 4500000.0],
                ]
            ],
            [
                'desc' => 'Penjualan seragam sekolah tunai (Bank)',
                'date' => Carbon::now()->subDays(54),
                'details' => [
                    ['account' => $bank, 'debit' => 25000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 25000000.0],
                    ['account' => $cogs, 'debit' => 15000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 15000000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran listrik & internet kantor',
                'date' => Carbon::now()->subDays(52),
                'details' => [
                    ['account' => $operational, 'debit' => 1200000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 1200000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran sebagian utang bahan kancing',
                'date' => Carbon::now()->subDays(50),
                'details' => [
                    ['account' => $payable, 'debit' => 2500000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 2500000.0],
                ]
            ],
            [
                'desc' => 'Penjualan jas almamater tunai (Kas)',
                'date' => Carbon::now()->subDays(48),
                'details' => [
                    ['account' => $cash, 'debit' => 18000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 18000000.0],
                    ['account' => $cogs, 'debit' => 11000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 11000000.0],
                ]
            ],
            [
                'desc' => 'Biaya sewa ruko operasional konveksi',
                'date' => Carbon::now()->subDays(46),
                'details' => [
                    ['account' => $operational, 'debit' => 5000000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 5000000.0],
                ]
            ],
            [
                'desc' => 'Beli jarum & perlengkapan mesin jahit',
                'date' => Carbon::now()->subDays(44),
                'details' => [
                    ['account' => $operational, 'debit' => 350000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 350000.0],
                ]
            ],
            [
                'desc' => 'Penjualan kaos komunitas sablon tunai',
                'date' => Carbon::now()->subDays(42),
                'details' => [
                    ['account' => $bank, 'debit' => 9500000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 9500000.0],
                    ['account' => $cogs, 'debit' => 5800000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 5800000.0],
                ]
            ],
            [
                'desc' => 'Biaya perawatan mesin jahit berkala',
                'date' => Carbon::now()->subDays(40),
                'details' => [
                    ['account' => $operational, 'debit' => 850000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 850000.0],
                ]
            ],
            [
                'desc' => 'Pembelian bahan pewarna kain impor kredit',
                'date' => Carbon::now()->subDays(38),
                'details' => [
                    ['account' => $inventory, 'debit' => 7800000.0, 'credit' => 0.0],
                    ['account' => $payable, 'debit' => 0.0, 'credit' => 7800000.0],
                ]
            ],
            [
                'desc' => 'Penjualan kemeja PDL kantor tunai (Bank)',
                'date' => Carbon::now()->subDays(36),
                'details' => [
                    ['account' => $bank, 'debit' => 32000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 32000000.0],
                    ['account' => $cogs, 'debit' => 20000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 20000000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran gaji karyawan bulanan konveksi',
                'date' => Carbon::now()->subDays(34),
                'details' => [
                    ['account' => $operational, 'debit' => 15000000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 15000000.0],
                ]
            ],
            [
                'desc' => 'Beli bensin mobil operasional antar jemput',
                'date' => Carbon::now()->subDays(32),
                'details' => [
                    ['account' => $operational, 'debit' => 450000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 450000.0],
                ]
            ],
            [
                'desc' => 'Lunas sisa utang pembelian kancing',
                'date' => Carbon::now()->subDays(30),
                'details' => [
                    ['account' => $payable, 'debit' => 2000000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 2000000.0],
                ]
            ],
            [
                'desc' => 'Penjualan celana training olahraga tunai',
                'date' => Carbon::now()->subDays(28),
                'details' => [
                    ['account' => $cash, 'debit' => 14000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 14000000.0],
                    ['account' => $cogs, 'debit' => 8500000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 8500000.0],
                ]
            ],
            [
                'desc' => 'Beli plastik packaging & label merk baju',
                'date' => Carbon::now()->subDays(26),
                'details' => [
                    ['account' => $operational, 'debit' => 950000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 950000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran tagihan air bersih PDAM ruko',
                'date' => Carbon::now()->subDays(24),
                'details' => [
                    ['account' => $operational, 'debit' => 600000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 600000.0],
                ]
            ],
            [
                'desc' => 'Penjualan seragam olahraga SD tunai (Bank)',
                'date' => Carbon::now()->subDays(22),
                'details' => [
                    ['account' => $bank, 'debit' => 16500000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 16500000.0],
                    ['account' => $cogs, 'debit' => 10000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 10000000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran sebahagian utang pewarna impor',
                'date' => Carbon::now()->subDays(20),
                'details' => [
                    ['account' => $payable, 'debit' => 4000000.0, 'credit' => 0.0],
                    ['account' => $bank, 'debit' => 0.0, 'credit' => 4000000.0],
                ]
            ],
            [
                'desc' => 'Beli alat tulis kantor & buku kwitansi',
                'date' => Carbon::now()->subDays(18),
                'details' => [
                    ['account' => $operational, 'debit' => 250000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 250000.0],
                ]
            ],
            [
                'desc' => 'Penjualan jas blazer wanita tunai (Kas)',
                'date' => Carbon::now()->subDays(16),
                'details' => [
                    ['account' => $cash, 'debit' => 21000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 21000000.0],
                    ['account' => $cogs, 'debit' => 13000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 13000000.0],
                ]
            ],
            [
                'desc' => 'Beli konsumsi lembur karyawan konveksi',
                'date' => Carbon::now()->subDays(14),
                'details' => [
                    ['account' => $operational, 'debit' => 550000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 550000.0],
                ]
            ],
            [
                'desc' => 'Pembelian bahan resleting & karet celana tunai',
                'date' => Carbon::now()->subDays(12),
                'details' => [
                    ['account' => $inventory, 'debit' => 3800000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 3800000.0],
                ]
            ],
            [
                'desc' => 'Penjualan seragam dinas harian PNS tunai',
                'date' => Carbon::now()->subDays(10),
                'details' => [
                    ['account' => $bank, 'debit' => 45000000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 45000000.0],
                    ['account' => $cogs, 'debit' => 28000000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 28000000.0],
                ]
            ],
            [
                'desc' => 'Pembayaran tagihan telepon operasional ruko',
                'date' => Carbon::now()->subDays(8),
                'details' => [
                    ['account' => $operational, 'debit' => 380000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 380000.0],
                ]
            ],
            [
                'desc' => 'Beli masker kain & hand sanitizer karyawan',
                'date' => Carbon::now()->subDays(6),
                'details' => [
                    ['account' => $operational, 'debit' => 180000.0, 'credit' => 0.0],
                    ['account' => $cash, 'debit' => 0.0, 'credit' => 180000.0],
                ]
            ],
            [
                'desc' => 'Penjualan rompi rajut pesanan tunai',
                'date' => Carbon::now()->subDays(4),
                'details' => [
                    ['account' => $bank, 'debit' => 12500000.0, 'credit' => 0.0],
                    ['account' => $sales, 'debit' => 0.0, 'credit' => 12500000.0],
                    ['account' => $cogs, 'debit' => 7500000.0, 'credit' => 0.0],
                    ['account' => $inventory, 'debit' => 0.0, 'credit' => 7500000.0],
                ]
            ],
        ];

        // Seed to database
        foreach ($transactions as $tx) {
            $entry = JournalEntry::create([
                'date' => $tx['date'],
                'description' => $tx['desc'],
                'reference_type' => 'manual',
                'reference_id' => null,
            ]);

            foreach ($tx['details'] as $detail) {
                JournalDetail::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $detail['account']->id,
                    'debit' => $detail['debit'],
                    'credit' => $detail['credit'],
                ]);
            }
        }

        $this->command->info('Successfully seeded 30 general journal entries.');
    }
}
