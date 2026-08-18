<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_rendered_for_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Admin Panel');
    }

    public function test_guests_are_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_users_can_login_and_reach_the_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@dml-blog.test',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@dml-blog.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Welcome back');
    }

    public function test_users_cannot_login_with_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@dml-blog.test',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@dml-blog.test',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
