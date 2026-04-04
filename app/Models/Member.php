<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'member_code', 'plan_id', 
        'join_date', 'expiry_date', 'grace_days', 'status', 'balance'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
