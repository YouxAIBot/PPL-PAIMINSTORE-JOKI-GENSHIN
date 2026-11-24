<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function loginAs(User $user, string $password = 'password123'): string
{
    return test()->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => $password,
    ])->json('data.token');
}

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Firaas',
        'email' => 'firaas@example.test',
        'password' => 'password123',
    ]);
});

it('menampilkan profil pengguna yang login', function () {
    $this->withToken(loginAs($this->user))
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('data.name', 'Firaas')
        ->assertJsonPath('data.email', 'firaas@example.test')
        ->assertJsonPath('data.email_verified', true);
});

it('mengubah nama dan email', function () {
    $this->withToken(loginAs($this->user))
        ->patchJson('/api/v1/me', ['name' => 'Firaas Baru', 'email' => 'baru@example.test'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Firaas Baru')
        ->assertJsonPath('data.email_verified', false);

    expect($this->user->fresh()->email)->toBe('baru@example.test');
});

it('menolak email yang dipakai akun lain', function () {
    User::factory()->create(['email' => 'lain@example.test']);

    $this->withToken(loginAs($this->user))
        ->patchJson('/api/v1/me', ['name' => 'Firaas', 'email' => 'lain@example.test'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('mengganti password dan mencabut token perangkat lain', function () {
    $tokenA = loginAs($this->user);
    loginAs($this->user);

    $this->withToken($tokenA)->putJson('/api/v1/me/password', [
        'current_password' => 'password123',
        'password' => 'passwordBaru1',
        'password_confirmation' => 'passwordBaru1',
    ])->assertOk();

    expect($this->user->tokens()->count())->toBe(1);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'firaas@example.test',
        'password' => 'passwordBaru1',
    ])->assertOk();
});

it('menolak ganti password jika password lama salah', function () {
    $this->withToken(loginAs($this->user))
        ->putJson('/api/v1/me/password', [
            'current_password' => 'salah',
            'password' => 'passwordBaru1',
            'password_confirmation' => 'passwordBaru1',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.current_password.0', 'Password lama salah');
});
