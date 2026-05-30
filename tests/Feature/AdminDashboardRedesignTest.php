<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Transaction;
use App\Models\PreOrder;
use App\Models\TransactionPayment;
use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductType;
use App\Models\TransactionDetail;
use App\Models\SizeOption;
use App\Models\Stock;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\OrderChartWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\TopProductsWidget;
use App\Filament\Widgets\TopTransactionsWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class AdminDashboardRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'Administrator', 'is_active' => true]);

        // Add dummy permission for admin panel
        $permission = Permission::create([
            'menu_name' => 'Dashboard',
            'route' => 'admin',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);
    }

    public function test_authenticated_user_can_access_dashboard_page(): void
    {
        $response = $this->actingAs($this->user)->get('/admin');
        $response->assertOk();
    }

    public function test_stats_overview_widget_calculates_correct_metrics(): void
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $client = \App\Models\Client::create([
            'client_name' => 'John Customer',
            'phone_number' => '081234567890',
        ]);

        // Create 2 transactions in current month
        $trx1 = Transaction::create([
            'trx_id' => 'INV0526-00001',
            'client_id' => $client->id,
            'total_price' => 500000,
            'total_discount' => 0,
            'grand_total' => 500000,
            'status' => 'on progress',
            'payment_status' => 'unpaid',
        ]);

        $trx2 = Transaction::create([
            'trx_id' => 'INV0526-00002',
            'client_id' => $client->id,
            'total_price' => 300000,
            'total_discount' => 0,
            'grand_total' => 300000,
            'status' => 'done',
            'payment_status' => 'paid',
        ]);

        // Create a cancelled transaction (should not sum in revenue)
        Transaction::create([
            'trx_id' => 'INV0526-00003',
            'client_id' => $client->id,
            'total_price' => 200000,
            'total_discount' => 0,
            'grand_total' => 200000,
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);

        // Create 1 pre-order in current month
        PreOrder::create([
            'po_id' => 'PO-0001',
            'client_id' => $client->id,
            'transaction_id' => null,
            'total_price' => 400000,
            'total_discount' => 0,
            'grand_total' => 400000,
            'status' => 'on process',
        ]);

        // Create a payment for trx2
        TransactionPayment::create([
            'transaction_id' => $trx2->id,
            'payment_date' => now(),
            'bank_name' => 'BCA',
            'account_number' => '12345678',
            'amount' => 300000,
        ]);

        $currentMonthYear = now()->translatedFormat('F Y');

        // Assert stats rendering
        Livewire::test(StatsOverview::class)
            ->assertSee('Total Orders')
            ->assertSee($currentMonthYear)
            ->assertSee('3') // 3 transactions total
            ->assertSee('Total POs')
            ->assertSee('1')
            ->assertSee('Total Revenue')
            ->assertSee('Rp 800.000') // sum of 500000 + 300000 (cancelled ignored)
            ->assertSee('Outstanding')
            ->assertSee('Remaining unpaid')
            ->assertSee('Rp 500.000') // 800000 - 300000
            ->assertSee('Total Payments')
            ->assertSee('Rp 300.000');
    }

    public function test_charts_and_tables_widgets_render_correctly(): void
    {
        $client = \App\Models\Client::create([
            'client_name' => 'Jane Customer',
            'phone_number' => '081234567891',
        ]);

        $product = Product::create(['product_name' => 'Premium Hoodie']);
        $productType = ProductType::create(['name' => 'Outerwear']);
        $variant = Variant::create([
            'product_id' => $product->id,
            'product_type_id' => $productType->id,
            'variant_code' => 'VAR-HD-01',
            'variant_name' => 'Jet Black',
            'color' => '#000000',
        ]);

        $sizeOption = SizeOption::firstOrCreate(
            ['name' => 'L'],
            [
                'order' => 1,
                'status' => 'active',
            ]
        );

        $trx = Transaction::create([
            'trx_id' => 'INV0526-00001',
            'client_id' => $client->id,
            'total_price' => 150000,
            'total_discount' => 0,
            'grand_total' => 150000,
            'status' => 'done',
        ]);

        TransactionDetail::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'size_option_id' => $sizeOption->id,
            'price' => 150000,
            'quantity' => 5,
            'discount' => 0,
            'subtotal' => 150000,
        ]);

        // Assert OrderChart renders
        Livewire::test(OrderChartWidget::class)
            ->assertOk();

        // Assert RevenueChart renders
        Livewire::test(RevenueChartWidget::class)
            ->assertOk();

        // Assert TopProducts renders
        Livewire::test(TopProductsWidget::class)
            ->assertSee('Premium Hoodie')
            ->assertSee('5');

        // Assert TopTransactions renders
        Livewire::test(TopTransactionsWidget::class)
            ->assertSee('Jane Customer')
            ->assertSee('Rp 150.000');
    }
}
