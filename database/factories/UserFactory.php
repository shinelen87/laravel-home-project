<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName,
            'lastname' => fake()->lastName,
            'phone' => fake()->unique()->phoneNumber,
            'birthdate' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if (! $user->hasAnyRole(Role::values())) {
                $user->assignRole(Role::CUSTOMER->value);
            }
        });
    }

    public function admin(): static
    {
        return $this->state(
            fn (array $attrs) => ['email' => 'admin@admin.com']
        )->afterCreating(function (User $user) {
            $user->syncRoles(Role::ADMIN->value);
        });
    }

    public function moderator(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->syncRoles(Role::MODERATOR->value);
        });
    }

    public function withEmail(string $email): static
    {
        return $this->state(fn (array $attrs) => ['email' => $email]);
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
