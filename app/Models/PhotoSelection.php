<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'session_id', 'customer_id', 'drive_file_id', 'file_name', 'is_final',
])]
class PhotoSelection extends Model
{
    public function session()
    {
        return $this->belongsTo(PhotoSession::class, 'session_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
