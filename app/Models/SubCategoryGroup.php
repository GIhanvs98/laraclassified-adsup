<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategoryGroup extends Model
{
    use HasFactory;

    protected $table = 'sub_category_groups';

    protected $fillable = ['name'];

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function categories(): BelongsToMany
    {
        //return $this->belongsToMany(Category::class);
        return $this->belongsToMany(Category::class, 'category_sub_category_group', 'sub_category_group_id', 'category_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(SubCategoryGroup::class, 'sub_category_group_id', 'id');
    }
}
