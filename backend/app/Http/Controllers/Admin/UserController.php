<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn ($w) => $w->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('email', 'like', '%'.$request->input('search').'%'));
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'role' => $request->input('role'),
            'status' => $request->input('status'),
        ]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user->load(['orders.package', 'assignedOrders', 'reviews']),
        ]);
    }

    public function ban(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Tidak bisa memban diri sendiri.']);
        }

        $user->update(['status' => 'banned']);

        ActivityLog::record(auth()->user(), 'user.banned', $user);

        return back()->with('success', "{$user->name} dibanned.");
    }

    public function unban(User $user)
    {
        $user->update(['status' => 'active']);

        ActivityLog::record(auth()->user(), 'user.unbanned', $user);

        return back()->with('success', "Ban {$user->name} dicabut.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $password = Str::random(12);

        $user->update(['password' => $password]);

        ActivityLog::record(auth()->user(), 'user.password_reset', $user);

        return back()->with([
            'success' => "Password {$user->email} direset.",
            'temp_password' => $password,
        ]);
    }
}
