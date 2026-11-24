<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BoosterSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'booster']);

        $boosters = [
            ['name' => 'Booster Satu', 'email' => 'booster1@paiminstore.test'],
            ['name' => 'Booster Dua', 'email' => 'booster2@paiminstore.test'],
        ];

        foreach ($boosters as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Booster,
                    'status' => UserStatus::Active,
                ]
            );

            $user->assignRole('booster');
        }
    }
}
