<?php

namespace App\Models;

use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InspectionPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
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
        static::creating(function (InspectionPublication $publication) {
            if (!$publication->slug && $publication->inspection) {
                $publication->slug = static::generateSlug($publication->inspection);
            }
        });
    }

    public static function generateSlug(Inspection $inspection): string
    {
        return Str::slug($inspection->project->name) . '-' . $inspection->id;
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
