<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function registerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Firaas',
        'email' => 'firaas@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

it('mendaftarkan pengguna baru sebagai customer', function () {
    $this->postJson('/api/v1/auth/register', registerPayload())
        ->assertCreated()
        ->assertJsonPath('data.email', 'firaas@example.test')
        ->assertJsonPath('data.role', 'customer');

    $user = User::where('email', 'firaas@example.test')->firstOrFail();

    expect($user->role)->toBe(UserRole::Customer)
        ->and($user->status)->toBe(UserStatus::Active)
        ->and($user->password)->not->toBe('password123');
});

it('menolak registrasi dengan email yang sudah terdaftar', function () {
    User::factory()->create(['email' => 'firaas@example.test']);

    $this->postJson('/api/v1/auth/register', registerPayload())
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('menolak registrasi dengan password pendek atau tidak cocok', function () {
    $this->postJson('/api/v1/auth/register', registerPayload([
        'password' => 'pendek',
        'password_confirmation' => 'beda',
    ]))->assertUnprocessable()->assertJsonValidationErrors('password');
});

it('login mengembalikan token dan data pengguna', function () {
    User::factory()->create(['email' => 'firaas@example.test', 'password' => 'password123']);

    $this->postJson('/api/v1/auth/login', ['email' => 'firaas@example.test', 'password' => 'password123'])
        ->assertOk()
        ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email', 'role']]]);
});

it('menolak login dengan password salah', function () {
    User::factory()->create(['email' => 'firaas@example.test', 'password' => 'password123']);

    $this->postJson('/api/v1/auth/login', ['email' => 'firaas@example.test', 'password' => 'salah'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Email atau password salah');
});

it('menolak login akun yang diblokir', function () {
    User::factory()->create([
        'email' => 'firaas@example.test',
        'password' => 'password123',
        'status' => UserStatus::Banned,
    ]);

    $this->postJson('/api/v1/auth/login', ['email' => 'firaas@example.test', 'password' => 'password123'])
        ->assertForbidden();
});

it('menolak akses tanpa token dengan jawaban JSON', function () {
    $this->getJson('/api/v1/me')->assertUnauthorized();
    $this->getJson('/api/v1/categories')->assertUnauthorized();
});

it('logout mencabut token yang dipakai', function () {
    User::factory()->create(['email' => 'firaas@example.test', 'password' => 'password123']);

    $token = $this->postJson('/api/v1/auth/login', [
        'email' => 'firaas@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

    expect(\Laravel\Sanctum\PersonalAccessToken::count())->toBe(0);
});

it('pengguna yang diblokir setelah login kehilangan token dan ditolak', function () {
    $user = User::factory()->create(['email' => 'firaas@example.test', 'password' => 'password123']);

    $token = $this->postJson('/api/v1/auth/login', [
        'email' => 'firaas@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $user->update(['status' => UserStatus::Banned]);

    $this->withToken($token)->getJson('/api/v1/me')->assertUnauthorized();

    expect($user->tokens()->count())->toBe(0);
});
