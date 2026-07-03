<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\ConsensusModel;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'url',
        'owner_id',
        'icon',
        'color',
        'require_evidence_for_high',
        'consensus_model',
        'is_self_assessment',
    ];

    protected $casts = [
        'require_evidence_for_high' => 'boolean',
        'consensus_model' => ConsensusModel::class,
        'is_self_assessment' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Get the invitations for the project.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the inspections for the project.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function evaluationRounds(): HasMany
    {
        return $this->hasMany(EvaluationRound::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getMemberRole(User $user): ?string
    {
        return $this->members()->where('user_id', $user->id)->value('role');
    }

}
