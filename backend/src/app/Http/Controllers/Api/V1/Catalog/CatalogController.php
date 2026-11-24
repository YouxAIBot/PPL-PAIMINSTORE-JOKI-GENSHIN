<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\PackageResource;
use App\Models\JokiCategory;
use App\Models\JokiPackage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function categories(): AnonymousResourceCollection
    {
        $categories = JokiCategory::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function packages(string $slug): AnonymousResourceCollection
    {
        $category = JokiCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $packages = $category->packages()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return PackageResource::collection($packages);
    }

    public function package(int $id): PackageResource
    {
        $package = JokiPackage::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->findOrFail($id);

        return new PackageResource($package);
    }
}
