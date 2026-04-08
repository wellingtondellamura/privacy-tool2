<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use App\Enums\ResponseProfile;

use App\Services\ResponseLabelResolver;
use Spatie\Translatable\HasTranslations;

class Section extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['questionnaire_version_id', 'name', 'order', 'response_profile'];

    protected $casts = [
        'response_profile' => ResponseProfile::class,
    ];

    protected $appends = ['options'];

    public function getOptionsAttribute(): array
    {
        return ResponseLabelResolver::optionsForProfile($this->response_profile);
    }

    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireVersion::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, Category::class);
    }
}
