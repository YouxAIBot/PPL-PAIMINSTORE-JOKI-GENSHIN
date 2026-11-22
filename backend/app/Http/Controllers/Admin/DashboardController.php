<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'roleLabel' => 'Owner / Admin',
            'todayOrders' => Order::whereDate('created_at', today())->count(),
            'todayRevenue' => (float) Payment::where('status', Payment::PAID)
                ->whereDate('paid_at', today())
                ->sum(DB::raw('amount - refunded_amount')),
            'queueCount' => Order::where('status', Order::QUEUED)->count(),
            'needVerification' => Payment::where('status', Payment::PENDING)->count(),
            'activeQueue' => Order::with(['user', 'package'])
                ->whereIn('status', [Order::QUEUED, Order::IN_PROGRESS])
                ->orderBy('queue_priority')->orderBy('id')
                ->limit(10)->get(),
            'verifications' => Payment::with(['order.user', 'order.package'])
                ->where('status', Payment::PENDING)
                ->latest()->limit(8)->get(),
        ]);
    }
}
