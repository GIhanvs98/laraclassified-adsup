<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThumbnailImage extends Model
{
    use HasFactory;

    protected $table = "thumbnail_images";

    protected $fillable = [
        'filename',
        'picture_id',
        'mime_type',
        'active',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function picture(): BelongsTo
    {
        return $this->belongsTo(Picture::class);
    }

}
