<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\MidtransWebhookRequest;
use App\Services\Payment\MidtransWebhookService;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/payments/midtrans/webhook
 * Endpoint tanpa auth: hanya menerima notifikasi dari Midtrans,
 * keabsahannya dibuktikan dengan signature SHA-512.
 */
class MidtransWebhookController extends Controller
{
    public function __invoke(MidtransWebhookRequest $request, MidtransWebhookService $service): JsonResponse
    {
        $result = $service->process($request->validated(), $request->ip());

        return response()->json($result['body'], $result['code']);
    }
}
