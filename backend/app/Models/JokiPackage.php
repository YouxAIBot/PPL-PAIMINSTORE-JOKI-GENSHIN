<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JokiPackage extends Model
{
    protected $fillable = [
        'category_id', 'name', 'tier', 'description', 'price', 'estimated_hours', 'is_active',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(JokiCategory::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
