<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['feature_id', 'user_id', 'event', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable description of the event.
     */
    public function getDescriptionAttribute(): string
    {
        return match ($this->event) {
            'status_changed'    => "Status changed from {$this->meta['from']} to {$this->meta['to']}",
            'approval_sent'     => 'Sent to client for approval',
            'approval_received' => "Client decision: {$this->meta['decision']}",
            'note_added'        => 'Note added',
            default             => $this->event,
        };
    }
}
