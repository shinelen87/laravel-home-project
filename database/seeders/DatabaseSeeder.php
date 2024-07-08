<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsAndRolesSeeder::class,
            UserSeeder::class,
            CategoryProductSeeder::class,
            ImageSeeder::class,
        ]);
    }
}
