<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JokiCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => JokiCategory::withCount('packages')->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $category = JokiCategory::create($data);

        ActivityLog::record(auth()->user(), 'category.created', $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori ditambahkan.');
    }

    public function edit(JokiCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, JokiCategory $category)
    {
        $data = $this->validateData($request, $category);
        $category->update($data);

        ActivityLog::record(auth()->user(), 'category.updated', $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(JokiCategory $category)
    {
        if ($category->packages()->exists()) {
            return back()->withErrors(['delete' => 'Kategori masih dipakai paket, nonaktifkan saja.']);
        }

        ActivityLog::record(auth()->user(), 'category.deleted', $category);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dihapus.');
    }

    private function validateData(Request $request, ?JokiCategory $category = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100', 'unique:joki_categories,name,'.$category?->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['slug'] = Str::slug($data['name']).'-'.substr((string) Str::uuid(), 0, 6);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
