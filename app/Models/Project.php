<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'client_name', 'client_email',
        'brief', 'stack', 'tech_tags', 'status', 'color',
        'deadline', 'share_token', 'share_enabled', 'shipped_at',
    ];

    protected $casts = [
        'tech_tags'      => 'array',
        'share_enabled'  => 'boolean',
        'deadline'       => 'date',
        'shipped_at'     => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class)->orderBy('sort_order');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function aiSuggestions(): HasMany
    {
        return $this->hasMany(AiSuggestion::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planning', 'active', 'review']);
    }

    public function scopeArchived($query)
    {
        return $query->whereIn('status', ['shipped', 'archived']);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function enableSharing(): string
    {
        $this->update([
            'share_token'   => Str::random(40),
            'share_enabled' => true,
        ]);

        return $this->share_token;
    }

    public function disableSharing(): void
    {
        $this->update(['share_enabled' => false]);
    }

    /**
     * Build the context string the AI will read.
     * Contains project brief, stack, and current features.
     */
    public function buildAiContext(): string
    {
        $features = $this->features()
            ->whereNotIn('status', ['cancelled'])
            ->pluck('title')
            ->join(', ');

        return <<<CONTEXT
        Project: {$this->name}
        Client: {$this->client_name}
        Stack: {$this->stack}
        Brief: {$this->brief}
        Existing features: {$features}
        CONTEXT;
    }

    // ── Stats ─────────────────────────────────────────────────────

    public function getProgressAttribute(): int
    {
        $total = $this->features()->count();
        if ($total === 0) return 0;

        $shipped = $this->features()->where('status', 'shipped')->count();
        return (int) round(($shipped / $total) * 100);
    }

    public function getPendingApprovalCountAttribute(): int
    {
        return $this->features()->where('status', 'awaiting_approval')->count();
    }
}
