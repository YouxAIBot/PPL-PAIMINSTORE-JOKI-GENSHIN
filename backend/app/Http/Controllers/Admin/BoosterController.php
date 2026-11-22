<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class BoosterController extends Controller
{
    public function index()
    {
        $boosters = User::where('role', 'booster')
            ->withCount([
                'boostedOrders as active_orders' => fn ($q) => $q->whereIn('status', [Order::QUEUED, Order::IN_PROGRESS]),
                'boostedOrders as completed_orders',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.boosters.index', ['boosters' => $boosters]);
    }

    public function show(User $user)
    {
        if (! $user->isBooster()) {
            return redirect()->route('admin.boosters.index')->withErrors(['booster' => 'Pengguna itu bukan booster.']);
        }

        $status = request()->input('status');
        $orders = $user->boostedOrders()
            ->with(['user', 'package', 'payment'])
            ->when($status && in_array($status, Order::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.boosters.show', [
            'booster' => $user,
            'orders' => $orders,
            'status' => $status,
            'statuses' => Order::STATUSES,
        ]);
    }
}
