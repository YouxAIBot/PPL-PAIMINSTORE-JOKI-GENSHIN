<?php

use App\Models\JokiCategory;
use App\Models\JokiPackage;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
    $this->seed(CatalogSeeder::class);
});

it('menampilkan kategori aktif', function () {
    $this->getJson('/api/v1/categories')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.slug', 'joki-explore-map');
});

it('menyembunyikan kategori yang dinonaktifkan', function () {
    JokiCategory::where('slug', 'joki-quest')->update(['is_active' => false]);

    $this->getJson('/api/v1/categories')->assertJsonCount(2, 'data');
    $this->getJson('/api/v1/categories/joki-quest/packages')->assertNotFound();
});

it('menampilkan paket per kategori dengan harga integer', function () {
    $response = $this->getJson('/api/v1/categories/joki-explore-map/packages')
        ->assertOk()
        ->assertJsonCount(8, 'data')
        ->assertJsonPath('data.0.name', 'Mondstadt 100%')
        ->assertJsonPath('data.0.estimated_hours', 36);

    expect($response->json('data.0.price'))->toBeInt()->toBe(75000);
});

it('menampilkan detail paket', function () {
    $package = JokiPackage::where('name', 'Fontaine 100%')->firstOrFail();

    $this->getJson('/api/v1/packages/' . $package->id)
        ->assertOk()
        ->assertJsonPath('data.name', 'Fontaine 100%')
        ->assertJsonPath('data.price', 175000);
});

it('menjawab 404 JSON untuk paket yang tidak ada atau nonaktif', function () {
    $package = JokiPackage::first();
    $package->update(['is_active' => false]);

    $this->getJson('/api/v1/packages/' . $package->id)
        ->assertNotFound()
        ->assertJsonPath('message', 'Data tidak ditemukan');

    $this->getJson('/api/v1/packages/9999')->assertNotFound();
});

it('seeder katalog aman dijalankan berulang', function () {
    $this->seed(CatalogSeeder::class);
    $this->seed(CatalogSeeder::class);

    expect(JokiCategory::count())->toBe(3)
        ->and(JokiPackage::count())->toBe(10);
});
