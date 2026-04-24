<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Collaborator extends Model
{
    protected $fillable = [
        'project_id', 'email', 'name', 'invite_token', 'role', 'accepted_at',
    ];

    protected $casts = ['accepted_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function invite(Project $project, string $email, string $role = 'editor'): self
    {
        return self::create([
            'project_id'   => $project->id,
            'email'        => $email,
            'role'         => $role,
            'invite_token' => Str::random(40),
        ]);
    }

    public function accept(): void
    {
        $this->update(['accepted_at' => now()]);
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null;
    }
}
