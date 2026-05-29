<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AppearanceSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppearanceSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_active' => true,
        ]);

        // Ensure there is at least one setting record
        AppearanceSetting::create([
            'header_logo' => null,
            'favicon' => null,
            'bank_logo' => null,
            'bank_account_number' => null,
            'bank_account_name' => null,
        ]);
    }

    public function test_guest_cannot_access_appearance_settings(): void
    {
        $response = $this->get(route('admin.appearance.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_can_access_appearance_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.appearance.index'));
        $response->assertOk();
        $response->assertSee('Bank Account Details');
    }

    public function test_user_can_update_bank_account_details(): void
    {
        Storage::fake('public');

        $bankLogo = UploadedFile::fake()->image('bank_logo.png');

        $response = $this->actingAs($this->user)
            ->post(route('admin.appearance.update'), [
                'bank_logo' => $bankLogo,
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'John Doe',
            ]);

        $response->assertRedirect(route('admin.appearance.index'));
        $response->assertSessionHas('success', 'Appearance settings updated successfully.');

        $setting = AppearanceSetting::first();
        $this->assertNotNull($setting->bank_logo);
        $this->assertEquals('1234567890', $setting->bank_account_number);
        $this->assertEquals('John Doe', $setting->bank_account_name);

        Storage::disk('public')->assertExists($setting->bank_logo);
    }
}
