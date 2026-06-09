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

    public $translatable = ['text'];
    protected $fillable = ['category_id', 'text', 'order'];

    public function getTextAttribute($value): ?string
    {
        return $this->resolveTranslatedValue('text', $value);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
