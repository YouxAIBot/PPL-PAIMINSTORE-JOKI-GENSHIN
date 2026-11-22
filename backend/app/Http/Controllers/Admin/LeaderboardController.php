<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = Cache::get('leaderboard_period', 'all');

        if (! in_array($request->input('period'), ['week', 'month', 'all', null], true)) {
            $period = 'all';
        } else {
            $period = $request->input('period', $period);
        }

        $start = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        $rows = User::query()
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', Order::COMPLETED)
            ->when($start, fn ($q) => $q->where('orders.completed_at', '>=', $start))
            ->selectRaw('users.id, users.name, users.show_on_leaderboard, SUM(orders.total_amount) AS total')
            ->groupBy('users.id', 'users.name', 'users.show_on_leaderboard')
            ->orderByDesc('total')
            ->limit(50)
            ->get()
            ->map(fn ($u) => [
                'name' => $u->show_on_leaderboard ? $u->name : 'Anonim',
                'total' => (float) $u->total,
            ]);

        return view('admin.leaderboard.index', [
            'rows' => $rows,
            'period' => $period,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->input('action') === 'reset') {
            Cache::forget('leaderboard_period');

            ActivityLog::record(auth()->user(), 'leaderboard.reset');

            return back()->with('success', 'Leaderboard direset ke periode semua waktu.');
        }

        $data = $request->validate([
            'period' => ['required', 'in:week,month,all'],
        ]);

        Cache::put('leaderboard_period', $data['period'], now()->addMonth());

        ActivityLog::record(auth()->user(), 'leaderboard.period', meta: ['period' => $data['period']]);

        return back()->with('success', 'Periode leaderboard diubah.');
    }
}
