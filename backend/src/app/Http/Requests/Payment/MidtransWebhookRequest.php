<?php

namespace App\Http\Requests\Payment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Validasi payload notifikasi Midtrans.
 * Kebabsahan signature diperiksa di MidtransWebhookService, bukan di sini.
 */
class MidtransWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sumbernya Midtrans; verifikasi dilakukan lewat signature SHA-512.
        return true;
    }

    /**
     * Webhook harus selalu dijawab JSON422, bukan redirect,
     * karena pengirim (Midtrans) tidak mengirim header Accept: application/json.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Payload tidak lengkap',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'string'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string'],
        ];
    }
}
