<?php

use App\Filament\Admin\Widgets\RevenueStatsWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('super_admin', 'web');

    $this->admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);
    $this->admin->assignRole('super_admin');

    $this->actingAs($this->admin);
});

it('menampilkan dashboard yang bisa diakses', function () {
    $this->get('/admin')->assertOk();
});

it('menampilkan ringkasan penjualan di widget dashboard', function () {
    // Widget dimuat lazy oleh Livewire, jadi diuji langsung pada komponennya.
    Livewire::test(RevenueStatsWidget::class)
        ->assertOk()
        ->assertSee('Ringkasan penjualan')
        ->assertSee('Total pemasukan')
        ->assertSee('Pemasukan bulan ini')
        ->assertSee('Order bulan ini');
});

it('menampilkan halaman daftar setiap resource', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/admin/joki-categories',
    '/admin/joki-packages',
    '/admin/orders',
    '/admin/payments',
    '/admin/reviews',
    '/admin/conversations',
    '/admin/audit-logs',
    '/admin/users',
]);

it('menampilkan form tambah kategori dan paket', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/admin/joki-categories/create',
    '/admin/joki-packages/create',
    '/admin/orders/create',
    '/admin/payments/create',
    '/admin/reviews/create',
    '/admin/users/create',
]);
