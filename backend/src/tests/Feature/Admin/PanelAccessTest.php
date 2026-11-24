<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('mengizinkan admin aktif masuk panel', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin, 'status' => UserStatus::Active]);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('menolak customer dan booster masuk panel', function (UserRole $role) {
    $user = User::factory()->create(['role' => $role, 'status' => UserStatus::Active]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
})->with([UserRole::Customer, UserRole::Booster]);

it('menolak admin yang diblokir masuk panel', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin, 'status' => UserStatus::Banned]);

    $this->actingAs($admin)->get('/admin')->assertForbidden();
});
