<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JokiCategory;
use App\Models\JokiPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        return view('admin.packages.index', [
            'packages' => JokiPackage::with('category')->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.packages.create', [
            'categories' => JokiCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $package = JokiPackage::create($data);

        ActivityLog::record(auth()->user(), 'package.created', $package);

        return redirect()->route('admin.packages.index')->with('success', 'Paket ditambahkan.');
    }

    public function edit(JokiPackage $package)
    {
        return view('admin.packages.edit', [
            'package' => $package,
            'categories' => JokiCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, JokiPackage $package)
    {
        $data = $this->validateData($request, $package);
        $package->update($data);

        ActivityLog::record(auth()->user(), 'package.updated', $package);

        return redirect()->route('admin.packages.index')->with('success', 'Paket diperbarui.');
    }

    public function destroy(JokiPackage $package)
    {
        if ($package->orders()->exists()) {
            return back()->withErrors(['delete' => 'Paket sudah punya order, nonaktifkan saja.']);
        }

        ActivityLog::record(auth()->user(), 'package.deleted', $package);
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Paket dihapus.');
    }

    private function validateData(Request $request, ?JokiPackage $package = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:joki_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'tier' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'estimated_hours' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
