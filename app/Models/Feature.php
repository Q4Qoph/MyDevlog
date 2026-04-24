<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Feature extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'title', 'description', 'status', 'priority',
        'type', 'needs_approval', 'approval_token', 'approval_requested_at',
        'ai_suggested', 'ai_rationale', 'sort_order', 'external_url',
        'started_at', 'shipped_at',
    ];

    protected $casts = [
        'needs_approval'         => 'boolean',
        'ai_suggested'           => 'boolean',
        'approval_requested_at'  => 'datetime',
        'started_at'             => 'datetime',
        'shipped_at'             => 'datetime',
    ];

    // ── Status constants ──────────────────────────────────────────

    const STATUS_BACKLOG            = 'backlog';
    const STATUS_AWAITING_APPROVAL  = 'awaiting_approval';
    const STATUS_APPROVED           = 'approved';
    const STATUS_BUILDING           = 'building';
    const STATUS_REVIEW             = 'review';
    const STATUS_SHIPPED            = 'shipped';
    const STATUS_CANCELLED          = 'cancelled';

    const STATUS_ORDER = [
        'backlog'           => 0,
        'awaiting_approval' => 1,
        'approved'          => 2,
        'building'          => 3,
        'review'            => 4,
        'shipped'           => 5,
        'cancelled'         => 6,
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(Approval::class)->latestOfMany();
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    // ── Approval helpers ──────────────────────────────────────────

    /**
     * Generate a unique approval token and mark as awaiting.
     */
    public function sendForApproval(): self
    {
        $this->update([
            'status'                 => self::STATUS_AWAITING_APPROVAL,
            'approval_token'         => Str::random(40),
            'approval_requested_at'  => now(),
        ]);

        $this->logActivity('approval_sent', [
            'token' => $this->approval_token,
        ]);

        return $this;
    }

    /**
     * Record client approval decision.
     */
    public function recordApproval(string $decision, ?string $note, ?string $clientEmail): Approval
    {
        $approval = $this->approvals()->create([
            'decision'     => $decision,
            'client_note'  => $note,
            'client_email' => $clientEmail,
            'decided_at'   => now(),
            'ip_address'   => request()->ip(),
        ]);

        $newStatus = $decision === 'approved'
            ? self::STATUS_APPROVED
            : self::STATUS_BACKLOG;

        $this->update(['status' => $newStatus]);

        $this->logActivity('approval_received', [
            'decision' => $decision,
            'note'     => $note,
        ]);

        return $approval;
    }

    // ── Status helpers ────────────────────────────────────────────

    public function advance(): self
    {
        $order  = self::STATUS_ORDER;
        $keys   = array_keys($order);
        $current = array_search($this->status, $keys);

        // Skip awaiting_approval when advancing manually
        $next = $keys[$current + 1] ?? $this->status;
        if ($next === self::STATUS_AWAITING_APPROVAL) {
            $next = $keys[$current + 2] ?? $this->status;
        }

        $this->updateStatus($next);
        return $this;
    }

    public function updateStatus(string $status): self
    {
        $old = $this->status;
        $this->update([
            'status'      => $status,
            'started_at'  => $status === self::STATUS_BUILDING && !$this->started_at ? now() : $this->started_at,
            'shipped_at'  => $status === self::STATUS_SHIPPED ? now() : $this->shipped_at,
        ]);

        $this->logActivity('status_changed', ['from' => $old, 'to' => $status]);
        return $this;
    }

    // ── Activity log helper ───────────────────────────────────────

    public function logActivity(string $event, array $meta = []): ActivityLog
    {
        return $this->activityLogs()->create([
            'user_id' => auth()->id(),
            'event'   => $event,
            'meta'    => $meta,
        ]);
    }
}
