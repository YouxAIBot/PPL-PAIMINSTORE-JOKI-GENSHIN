<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\JokiPackage
 */
class PackageResource extends JsonResource
{
    /**
     * Harga selalu integer rupiah, bukan string desimal.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (int) $this->price,
            'estimated_hours' => (int) $this->estimated_hours,
        ];
    }
}
