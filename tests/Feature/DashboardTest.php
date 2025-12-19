<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_admin_users_are_not_redirected_to_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($user)->get('/login');

        // Admin users accessing login should be redirected to dashboard
        $response->assertRedirect('/');
    }

    public function test_non_admin_users_cannot_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/');

        // Non-admin users should be forbidden
        $response->assertForbidden();
    }
}
