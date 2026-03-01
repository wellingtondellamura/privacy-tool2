<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inspection extends Model
{
    use HasFactory;

    public function publication(): HasOne
    {
        return $this->hasOne(InspectionPublication::class);
    }

    protected static function booted()
    {
        static::creating(function (Inspection $inspection) {
            if (!$inspection->sequential_id) {
                $inspection->sequential_id = (static::where('project_id', $inspection->project_id)->max('sequential_id') ?? 0) + 1;
            }
        });
    }

    protected $fillable = [
        'project_id',
        'sequential_id',
        'user_id',
        'questionnaire_version_id',
        'status',
        'started_at',
        'closed_at',
        'evaluation_round_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    // Valid state transitions: draft → active → closed
    private const TRANSITIONS = [
        'draft' => ['active'],
        'active' => ['closed'],
        'closed' => [],
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireVersion::class);
    }

    public function evaluationRound(): BelongsTo
    {
        return $this->belongsTo(EvaluationRound::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function resultSnapshots(): HasMany
    {
        return $this->hasMany(ResultSnapshot::class);
    }

    /**
     * Check if a transition is valid.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? []);
    }

    /**
     * Perform a state transition.
     *
     * @throws \InvalidArgumentException
     */
    public function transitionTo(string $newStatus): void
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$this->status}' to '{$newStatus}'."
            );
        }

        $this->status = $newStatus;

        if ($newStatus === 'active') {
            $this->started_at = now();
        }

        if ($newStatus === 'closed') {
            $this->closed_at = now();
        }

        $this->save();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
