<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['order.user'])
            ->when($request->filled('method'), fn ($q) => $q->where('method', $request->input('method')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'method' => $request->input('method'),
            'status' => $request->input('status'),
        ]);
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== Payment::PENDING) {
            return back()->withErrors(['payment' => 'Pembayaran sudah diproses.']);
        }

        if ($payment->method !== 'manual') {
            return back()->withErrors(['payment' => 'Hanya bukti transfer manual yang diverifikasi admin.']);
        }

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order) {
            $payment->update([
                'status' => Payment::PAID,
                'paid_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            if ($order->status === Order::PENDING_PAYMENT) {
                $order->update([
                    'status' => Order::QUEUED,
                    'paid_at' => now(),
                    'queue_priority' => $order->queue_priority ?: 0,
                ]);
            }
        });

        ActivityLog::record(auth()->user(), 'payment.approved', $payment, ['order_no' => $order->order_no]);
        ActivityLog::record(auth()->user(), 'order.status_changed', $order, [
            'from' => Order::PENDING_PAYMENT, 'to' => Order::QUEUED, 'via' => 'payment',
        ]);

        return back()->with('success', "Pembayaran #{$payment->id} disetujui, order masuk antrian.");
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);

        if ($payment->status !== Payment::PENDING) {
            return back()->withErrors(['payment' => 'Pembayaran sudah diproses.']);
        }

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order, $data) {
            $payment->update(['status' => Payment::FAILED]);

            if ($order->status === Order::PENDING_PAYMENT) {
                $order->update([
                    'status' => Order::CANCELLED,
                    'cancel_reason' => $data['reason'],
                ]);
            }
        });

        ActivityLog::record(auth()->user(), 'payment.rejected', $payment, ['reason' => $data['reason']]);

        return back()->with('success', "Pembayaran #{$payment->id} ditolak.");
    }

    public function refund(Request $request, Payment $payment)
    {
        $remaining = (float) ($payment->amount - $payment->refunded_amount);

        if ($remaining <= 0 || ! in_array($payment->status, [Payment::PAID, Payment::REFUNDED], true)) {
            return back()->withErrors(['payment' => 'Pembayaran ini tidak bisa direfund.']);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$remaining],
        ]);

        $newTotal = $payment->refunded_amount + $data['amount'];
        $isFull = $newTotal >= $payment->amount;

        DB::transaction(function () use ($payment, $newTotal, $isFull) {
            $payment->update([
                'refunded_amount' => $newTotal,
                'status' => $isFull ? Payment::REFUNDED : Payment::PAID,
                'refunded_at' => now(),
            ]);
        });

        ActivityLog::record(auth()->user(), 'payment.refunded', $payment, [
            'amount' => $data['amount'], 'full' => $isFull,
        ]);

        return back()->with('success', 'Refund Rp '.number_format($data['amount'], 0, ',', '.').' dicatat.');
    }
}
