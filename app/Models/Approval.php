<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    protected $fillable = [
        'feature_id', 'decision', 'client_note',
        'client_name', 'client_email', 'ip_address', 'decided_at',
    ];

    protected $casts = ['decided_at' => 'datetime'];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
