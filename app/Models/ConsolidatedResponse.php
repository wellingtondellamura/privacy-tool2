<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsolidatedResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_round_id',
        'question_id',
        'final_answer',
        'decided_by',
        'resolution_method',
    ];

    public function evaluationRound(): BelongsTo
    {
        return $this->belongsTo(EvaluationRound::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
