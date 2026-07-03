<?php

namespace App\Models;

use App\Models\Concerns\ResolvesLegacyTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasFactory, HasTranslations, ResolvesLegacyTranslations;

    public $translatable = ['text', 'tooltip', 'good_practice_example', 'bad_practice_example'];
    protected $fillable = ['category_id', 'text', 'order', 'tooltip', 'good_practice_example', 'bad_practice_example'];

    public function getTextAttribute($value): ?string
    {
        return $this->resolveTranslatedValue('text', $value);
    }

    public function getTooltipAttribute($value): ?string
    {
        return $this->resolveTranslatedValue('tooltip', $value);
    }

    public function getGoodPracticeExampleAttribute($value): ?string
    {
        return $this->resolveTranslatedValue('good_practice_example', $value);
    }

    public function getBadPracticeExampleAttribute($value): ?string
    {
        return $this->resolveTranslatedValue('bad_practice_example', $value);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
