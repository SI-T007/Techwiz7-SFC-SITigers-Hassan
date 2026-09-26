<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Join the Universe');
    }

    public function test_user_can_register_successfully(): void
    {
        $email = 'newhero_' . time() . '@fanhub.test';

        $response = $this->post('/register', [
            'name' => 'New Fandom Hero',
            'email' => $email,
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->is_active);

        // Verify profile was created automatically
        $this->assertNotNull($user->profile);
        $this->assertEquals('New Fandom Hero', $user->profile->display_name);
    }

    public function test_registration_validation_fails_for_duplicate_email(): void
    {
        $existing = User::first();
        if ($existing) {
            $response = $this->post('/register', [
                'name' => 'Duplicate Person',
                'email' => $existing->email,
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
                'terms' => 'on',
            ]);

            $response->assertSessionHasErrors(['email']);
        }
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::where('role', 'user')->where('is_active', true)->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive_' . time() . '@fanhub.test',
            'password' => Hash::make('Password@123'),
            'role' => 'user',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'Password@123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('My Fandom Dashboard');
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Gamer Name',
            'display_name' => 'NeoGamer99',
            'bio' => 'Updated gamer bio description.',
            'location' => 'Neo Tokyo',
            'website' => 'https://fanhubplus.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Updated Gamer Name', $user->name);
        $this->assertEquals('NeoGamer99', $user->profile->display_name);
    }

    public function test_authenticated_user_can_update_favorite_fandoms(): void
    {
        $user = User::where('role', 'user')->first();
        $categories = Category::take(2)->pluck('id')->toArray();

        $response = $this->actingAs($user)->post('/profile/fandoms', [
            'categories' => $categories,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertCount(2, $user->favoriteCategories);
    }

    public function test_authenticated_user_can_update_preferences(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->post('/profile/preferences', [
            'theme' => 'dark',
            'animations' => '1',
            'font_size' => 'large',
            'reduced_motion' => '0',
            'high_contrast' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('dark', $user->profile->display_preferences['theme']);
        $this->assertEquals('large', $user->profile->accessibility_preferences['font_size']);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'Password@123',
            'password' => 'NewPassword@456',
            'password_confirmation' => 'NewPassword@456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify new password works
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword@456', $user->password));

        // Restore password for other tests
        $user->update(['password' => Hash::make('Password@123')]);
    }
}
