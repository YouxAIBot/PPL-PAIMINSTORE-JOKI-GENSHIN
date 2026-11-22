<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('actor')
            ->when($request->filled('action'), fn ($q) => $q->where('action', 'like', $request->input('action').'%'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'action' => $request->input('action'),
        ]);
    }
}
