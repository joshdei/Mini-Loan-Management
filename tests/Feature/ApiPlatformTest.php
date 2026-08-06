<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ada Borrower',
            'email' => 'ada@example.com',
            'phone' => '08010000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'test',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.role', 'borrower')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_public_settings_endpoint_creates_default_settings(): void
    {
        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('data.site_name', 'My Loan Business');

        $this->assertDatabaseCount('settings', 1);
    }

    public function test_owner_can_update_settings(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'password' => Hash::make('password123'),
        ]);

        $token = $this->postJson('/api/login', [
            'email' => $owner->email,
            'password' => 'password123',
        ])->json('token');

        $this->withToken($token)
            ->putJson('/api/admin/settings', [
                'site_name' => 'Bright Credit',
                'primary_color' => '#12AB34',
                'currency_code' => 'NGN',
            ])
            ->assertOk()
            ->assertJsonPath('data.site_name', 'Bright Credit');

        $this->assertSame('Bright Credit', Setting::current()->site_name);
    }

    public function test_owner_login_keeps_only_latest_token(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/login', [
            'email' => $owner->email,
            'password' => 'password123',
        ])->assertOk();

        $this->postJson('/api/login', [
            'email' => $owner->email,
            'password' => 'password123',
        ])->assertOk();

        $this->assertSame(1, $owner->tokens()->count());
    }
}
