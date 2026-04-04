<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['category_id', 'name', 'duration_days', 'price', 'description', 'features', 'is_active'];

    public function category()
    {
        return $this->belongsTo(PlanCategory::class, 'category_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
