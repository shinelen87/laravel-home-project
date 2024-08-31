<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PermissionsAndRolesSeeder']);
    }

    public function test_registration_with_valid_data()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'lastname' => 'User',
            'phone' => '1234567890',
            'birthdate' => '2000-01-01',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/home');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_registration_with_invalid_data()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'different_password',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password', 'lastname', 'phone', 'birthdate']);
    }

    public function test_login_with_valid_data()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/account');
    }

    public function test_login_with_invalid_data()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_access_admin_panel()
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_moderator_can_access_admin_panel()
    {
        $moderator = User::factory()->create();
        $moderator->assignRole(Role::MODERATOR);

        $response = $this->actingAs($moderator)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_panel()
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::CUSTOMER);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
