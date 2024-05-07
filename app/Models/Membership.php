<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'memberships';

    protected $fillable = ['name', 'description', 'icon', 'allowed_ads', 'allowed_pictures', 'doorstep_delivery', 'amount', 'active','currency_code'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

     /**+
      * Summary of scopeDoesntExist
      * @param \Illuminate\Database\Eloquent\Builder $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
    public function scopeMember(Builder $query): Builder{
        return $query->whereNotIn('name', ['Non Member'])->whereNotIn('amount', [0, 0.00]); 
    }

     /**
      * Summary of scopeDoesntExist
      * @param \Illuminate\Database\Eloquent\Builder $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
    public function scopeNonMember(Builder $query): Builder{
        return $query->where('name', 'Non Member')->whereIn('amount', [0, 0.00]); 
    }
}
