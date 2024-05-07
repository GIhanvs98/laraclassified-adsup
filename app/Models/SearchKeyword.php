<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchKeyword extends Model
{
    use HasFactory;

    protected $fillable = ['keywords'];

    protected $casts = [
        'keywords' => 'string',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
