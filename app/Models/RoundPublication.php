<?php

namespace App\Models;

use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RoundPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_round_id',
        'visibility',
        'slug',
        'published_at',
        'published_by',
        'score',
        'medal',
        'year',
        'questionnaire_version_id',
    ];

    protected $casts = [
        'visibility' => Visibility::class,
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (RoundPublication $publication) {
            if (!$publication->slug && $publication->evaluationRound) {
                $publication->slug = static::generateSlug($publication->evaluationRound);
            }
        });
    }

    public static function generateSlug(EvaluationRound $round): string
    {
        $base = Str::slug($round->project->name);
        return $base . '-round-' . $round->id;
    }

    public function evaluationRound(): BelongsTo
    {
        return $this->belongsTo(EvaluationRound::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireVersion::class);
    }
}
