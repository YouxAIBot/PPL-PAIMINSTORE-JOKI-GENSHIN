<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JokiCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function packages()
    {
        return $this->hasMany(JokiPackage::class, 'category_id');
    }
}
