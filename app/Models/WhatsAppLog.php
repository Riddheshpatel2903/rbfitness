<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    protected $table = 'whatsapp_logs';
    protected $fillable = ['member_id', 'type', 'message', 'status'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
