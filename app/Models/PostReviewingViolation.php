<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReviewingViolation extends Model
{
    use HasFactory;

    protected $table = 'post_reviewing_violations';

    public $timestamps = false;

	protected $fillable = [
		'reason',
		'last_datetime', # Remaining days to complete revision and resubmition before auto deletion.
		'rechecked_datetime' # Date and time when user rechecked/updated the ad post.
	];

	public function post()
	{
		return $this->belongsTo(Post::class, 'post_id', 'id');
	}
	
	public function scopeTimeleft(Builder $query)
	{
		return $query->where('last_datetime', '>', now());
	}

	public function scopeTimeout(Builder $query)
	{
		return $query->where('last_datetime', '<=', now());
	}

	public function scopeRechecked(Builder $query)
	{
		return $query->whereNotNull('rechecked_datetime');
	}

	public function scopeNotRechecked(Builder $query)
	{
		return $query->whereNull('rechecked_datetime');
	}

	public function scopeRecheckedTimeleft(Builder $query)
	{
		return $query->rechecked()->timeleft();
	}

	public function scopeRecheckedTimeout(Builder $query)
	{
		return $query->rechecked()->timeout();
	}

	public function scopeNotRecheckedTimeleft(Builder $query)
	{
		return $query->notRechecked()->timeleft();
	}

	public function scopeNotRecheckedTimeout(Builder $query)
	{
		return $query->notRechecked()->timeout();
	}
}
