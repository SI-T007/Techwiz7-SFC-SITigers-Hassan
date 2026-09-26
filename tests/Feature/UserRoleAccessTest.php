<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserRoleAccessTest extends TestCase
{
    public function test_visitor_cannot_access_protected_routes(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/profile');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_standard_user_cannot_access_admin_dashboard(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        // Middleware EnsureUserIsAdmin aborts with 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Universe Control Center');
    }

    public function test_admin_can_view_users_directory(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('User Directory', false);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $initialStatus = $user->is_active;

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals(!$initialStatus, $user->is_active);

        // Restore original status
        $user->update(['is_active' => $initialStatus]);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post("/admin/users/{$admin->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $admin->refresh();
        $this->assertTrue($admin->is_active);
    }
}
