<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'customer_id', 'session_id', 'type', 'title', 'drive_file_id',
])]
class Document extends Model
{
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function session()
    {
        return $this->belongsTo(PhotoSession::class, 'session_id');
    }
}
