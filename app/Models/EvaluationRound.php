<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EvaluationRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'status',
        'diagnosis',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function publicDirectory(): HasOne
    {
        return $this->hasOne(RoundPublication::class);
    }

    public function badge(): HasOne
    {
        return $this->hasOne(RoundBadge::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(RoundSnapshot::class);
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isPublished(): bool
    {
        return $this->isClosed() && $this->publicDirectory()->exists();
    }
}
