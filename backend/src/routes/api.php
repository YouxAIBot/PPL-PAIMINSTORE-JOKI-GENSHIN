<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Catalog\CatalogController;
use App\Http\Controllers\Api\V1\Payment\MidtransWebhookController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix /api)
|--------------------------------------------------------------------------
| Versi 1 memakai prefix v1: /api/v1/...
| Endpoint order dan pembayaran gateway menyusul.
*/

Route::prefix('v1')->group(function () {
    // Webhook Midtrans: tanpa auth, diverifikasi lewat signature SHA-512
    Route::post('payments/midtrans/webhook', MidtransWebhookController::class);

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'not.banned'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('me', [ProfileController::class, 'show']);
        Route::patch('me', [ProfileController::class, 'update']);
        Route::put('me/password', [ProfileController::class, 'password']);

        Route::get('categories', [CatalogController::class, 'categories']);
        Route::get('categories/{slug}/packages', [CatalogController::class, 'packages']);
        Route::get('packages/{id}', [CatalogController::class, 'package'])->whereNumber('id');
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
