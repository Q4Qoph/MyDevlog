<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSuggestion extends Model
{
    protected $fillable = [
        'project_id', 'title', 'rationale', 'source_project',
        'status', 'generated_at',
    ];

    protected $casts = ['generated_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function accept(): Feature
    {
        $feature = $this->project->features()->create([
            'title'          => $this->title,
            'description'    => $this->rationale,
            'status'         => 'backlog',
            'ai_suggested'   => true,
            'ai_rationale'   => $this->rationale,
        ]);

        $this->update(['status' => 'accepted']);
        return $feature;
    }

    public function dismiss(): void
    {
        $this->update(['status' => 'dismissed']);
    }
}
