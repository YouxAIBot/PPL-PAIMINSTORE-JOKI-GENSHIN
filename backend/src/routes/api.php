<?php

use App\Http\Controllers\Api\V1\Payment\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix /api)
|--------------------------------------------------------------------------
| Versi 1 memakai prefix v1: /api/v1/...
| Endpoint lain (auth, katalog, order) menyusul setelah Sanctum disetujui tim.
*/

Route::prefix('v1')->group(function () {
    // Webhook Midtrans: tanpa auth, diverifikasi lewat signature SHA-512
    Route::post('payments/midtrans/webhook', MidtransWebhookController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
