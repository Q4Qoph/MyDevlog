<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMemory extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'project_name', 'stack_tags',
        'shipped_features', 'cut_features', 'ai_ignored',
        'retrospective', 'days_to_ship',
    ];

    protected $casts = [
        'stack_tags'       => 'array',
        'shipped_features' => 'array',
        'cut_features'     => 'array',
        'ai_ignored'       => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
