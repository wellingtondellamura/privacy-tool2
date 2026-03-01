<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoundSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_round_id',
        'payload_json',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];

    public function evaluationRound(): BelongsTo
    {
        return $this->belongsTo(EvaluationRound::class);
    }
}
