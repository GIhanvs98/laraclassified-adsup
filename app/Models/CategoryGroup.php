<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryGroup extends Model
{
    use HasFactory;

    protected $table = 'category_groups';
    
    protected $fillable = ['name', 'icon'];

    public function subCategoryGroups(): HasMany
    {
        return $this->hasMany(SubCategoryGroup::class);
    }
}
