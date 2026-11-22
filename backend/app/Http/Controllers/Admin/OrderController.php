<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JokiPackage;
use App\Models\Order;
use App\Models\OrderGameAccount;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $orders = Order::with(['user', 'package', 'booster', 'payment'])
            ->when($status && in_array($status, Order::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('order_no', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function create()
    {
        return view('admin.orders.create', [
            'customers' => User::where('role', 'user')->orderBy('name')->get(),
            'packages' => JokiPackage::with('category')->where('is_active', true)->orderBy('name')->get(),
            'boosters' => User::where('role', 'booster')->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'package_id' => ['required', 'exists:joki_packages,id'],
            'booster_id' => ['nullable', 'exists:users,id'],
            'uid' => ['required', 'string', 'max:100'],
            'server' => ['required', 'in:asia,europe,america,twhkmo'],
            'credential_username' => ['required', 'string', 'max:255'],
            'credential_password' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending_payment,queued'],
            'queue_priority' => ['nullable', 'integer', 'min:-999', 'max:999'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer = User::find($data['user_id']);
        $package = JokiPackage::find($data['package_id']);

        if ($customer?->role !== 'user') {
            return back()->withErrors(['user_id' => 'Pengguna itu bukan pelanggan.'])->withInput();
        }

        if (! $package || ! $package->is_active) {
            return back()->withErrors(['package_id' => 'Paket tidak aktif.'])->withInput();
        }

        if (! empty($data['booster_id'])) {
            $booster = User::find($data['booster_id']);
            if (! $booster?->isBooster() || $booster->status !== 'active') {
                return back()->withErrors(['booster_id' => 'Pengguna itu bukan booster aktif.'])->withInput();
            }
        }

        $orderNo = 'ORD-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $status = $data['status'];
        $isQueued = $status === Order::QUEUED;

        $order = DB::transaction(function () use ($data, $customer, $package, $orderNo, $status, $isQueued) {
            $order = Order::create([
                'order_no' => $orderNo,
                'user_id' => $customer->id,
                'package_id' => $package->id,
                'booster_id' => $data['booster_id'] ?? null,
                'status' => $status,
                'price_snapshot' => $package->price,
                'total_amount' => $package->price,
                'est_hours_snapshot' => $package->estimated_hours,
                'queue_priority' => $data['queue_priority'] ?? 0,
                'customer_notes' => $data['customer_notes'] ?? null,
                'paid_at' => $isQueued ? now() : null,
            ]);

            OrderGameAccount::create([
                'order_id' => $order->id,
                'uid' => $data['uid'],
                'server' => $data['server'],
                'credential_encrypted' => [
                    'username' => $data['credential_username'],
                    'password' => $data['credential_password'],
                ],
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway_ref' => 'MANUAL-'.$order->order_no,
                'method' => 'manual',
                'amount' => $package->price,
                'status' => $isQueued ? Payment::PAID : Payment::PENDING,
                'verified_by' => $isQueued ? auth()->id() : null,
                'paid_at' => $isQueued ? now() : null,
            ]);

            ActivityLog::record(auth()->user(), 'order.created', $order, [
                'status' => $status,
                'package_id' => $package->id,
            ]);

            return $order;
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order {$order->order_no} dibuat.");
    }

    public function show(Order $order)
    {
        $order->load(['user', 'package.category', 'booster', 'gameAccount', 'payment.verifier', 'reviews.user']);

        return view('admin.orders.show', [
            'order' => $order,
            'boosters' => User::where('role', 'booster')->where('status', 'active')->orderBy('name')->get(),
            'logs' => ActivityLog::where('entity_type', 'Order')->where('entity_id', $order->id)->latest()->limit(20)->get(),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);

        $target = $data['status'];

        if ($target === Order::CANCELLED) {
            return back()->withErrors(['status' => 'Gunakan form "Batalkan Order" (wajib isi alasan) untuk membatalkan.']);
        }

        $allowed = [
            Order::QUEUED => [Order::IN_PROGRESS],
            Order::IN_PROGRESS => [Order::COMPLETED],
        ];

        if (! in_array($target, $allowed[$order->status] ?? [], true)) {
            return back()->withErrors(['status' => 'Transisi status tidak valid dari '.$order->status.'.']);
        }

        $previous = $order->status;

        $order->status = $target;

        if ($target === Order::IN_PROGRESS) {
            $order->started_at = now();
        }

        if ($target === Order::COMPLETED) {
            $order->completed_at = now();
            if ($order->gameAccount) {
                $order->gameAccount->update(['purge_at' => now()->addDays(7)]);
            }
        }

        $order->save();

        ActivityLog::record(auth()->user(), 'order.status_changed', $order, [
            'from' => $previous, 'to' => $target,
        ]);

        return back()->with('success', "Status order {$order->order_no} menjadi {$target}.");
    }

    public function cancel(Request $request, Order $order)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        if (! $order->isCancellable()) {
            return back()->withErrors(['status' => "Order berstatus {$order->status} tidak bisa dibatalkan."]);
        }

        $previous = $order->status;

        $order->update([
            'status' => Order::CANCELLED,
            'cancel_reason' => $data['reason'],
        ]);

        ActivityLog::record(auth()->user(), 'order.cancelled', $order, [
            'from' => $previous, 'reason' => $data['reason'],
        ]);

        return back()->with('success', "Order {$order->order_no} dibatalkan.");
    }

    public function assignBooster(Request $request, Order $order)
    {
        $data = $request->validate([
            'booster_id' => ['required', 'exists:users,id'],
        ]);

        $booster = User::find($data['booster_id']);

        if (! $booster->isBooster() || $booster->status !== 'active') {
            return back()->withErrors(['booster_id' => 'Pengguna itu bukan booster aktif.']);
        }

        if (! in_array($order->status, [Order::QUEUED, Order::IN_PROGRESS], true)) {
            return back()->withErrors(['status' => 'Order harus berstatus antrian atau dikerjakan.']);
        }

        $previous = $order->booster_id;
        $order->booster_id = $booster->id;
        $order->save();

        ActivityLog::record(auth()->user(), 'order.booster_assigned', $order, [
            'booster_id' => $booster->id, 'from' => $previous,
        ]);

        return back()->with('success', "Booster {$booster->name} ditugaskan ke {$order->order_no}.");
    }

    public function updatePriority(Request $request, Order $order)
    {
        $data = $request->validate([
            'queue_priority' => ['required', 'integer', 'min:-999', 'max:999'],
        ]);

        if ($order->status !== Order::QUEUED) {
            return back()->withErrors(['status' => 'Hanya order antrian yang bisa diatur prioritasnya.']);
        }

        $order->queue_priority = $data['queue_priority'];
        $order->save();

        ActivityLog::record(auth()->user(), 'order.priority_changed', $order, [
            'priority' => $order->queue_priority,
        ]);

        return back()->with('success', 'Prioritas antrian diperbarui.');
    }

    public function showCredentials(Order $order)
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $order->booster_id !== $user->id) {
            abort(403, 'Kredensial hanya untuk booster yang ditugaskan dan admin.');
        }

        if (! $order->gameAccount) {
            return back()->withErrors(['credential' => 'Order ini belum punya kredensial akun game.']);
        }

        ActivityLog::record($user, 'game_account.accessed', $order->gameAccount);

        return view('admin.orders.credentials', [
            'order' => $order,
            'credential' => $order->gameAccount->credential_encrypted,
        ]);
    }
}
