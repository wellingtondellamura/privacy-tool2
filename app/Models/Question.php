<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['category_id', 'text', 'order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
