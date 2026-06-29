<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'customer_id', 'title', 'shoot_date', 'drive_folder_url',
    'drive_folder_id', 'max_selections', 'status', 'download_link', 'submitted_at',
])]
class PhotoSession extends Model
{
    protected function casts(): array
    {
        return [
            'shoot_date' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function photoSelections()
    {
        return $this->hasMany(PhotoSelection::class, 'session_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'session_id');
    }
}
