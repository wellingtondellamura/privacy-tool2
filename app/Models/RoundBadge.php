<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoundBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_round_id',
        'public_token',
        'style',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function evaluationRound(): BelongsTo
    {
        return $this->belongsTo(EvaluationRound::class);
    }
}
