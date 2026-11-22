<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Owner Paiminstore', 'owner@paiminstore.test', 'admin'],
            ['Booster Satu', 'booster1@paiminstore.test', 'booster'],
            ['Booster Dua', 'booster2@paiminstore.test', 'booster'],
            ['Pengguna Contoh', 'user@paiminstore.test', 'user'],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => 'password123',
                'role' => $role,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }
}
