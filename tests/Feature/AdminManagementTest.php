<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_order_and_sales_management_pages(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.sales.index'))
            ->assertOk();
    }

    public function test_locked_user_cannot_login(): void
    {
        User::create([
            'name' => 'Locked User',
            'email' => 'locked@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'is_locked' => true,
        ]);

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => 'locked@example.com',
                'password' => 'password123',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }
}
