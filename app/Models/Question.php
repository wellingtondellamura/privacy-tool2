<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['text'];
    protected $fillable = ['category_id', 'text', 'order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
