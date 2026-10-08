<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = config('app.admin_password');

        if (! is_string($adminPassword) || $adminPassword === '') {
            throw new \RuntimeException('Set TRIO_ADMIN_PASSWORD before seeding the database.');
        }

        User::updateOrCreate(
            ['email' => 'admin@trio.local'],
            [
                'name' => 'TRIO Admin',
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
            ]
        );

        // 2. Seed Filaments & Materials
        $this->call(FilamentSeeder::class);

        // 3. Seed Catalog Products
        $this->call(ProductSeeder::class);
    }
}
