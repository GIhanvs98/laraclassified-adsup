<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAd extends Model
{
    use HasFactory;

	protected $table = 'report_ads';

    protected $fillable = [
        'reason',
        'email',
        'message',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
