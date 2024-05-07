<?php

namespace App\Livewire\PostAd;

use App\Models\Category;
use Livewire\Component;
use App\Models\SubCategoryGroup;
use Illuminate\Database\Eloquent\Builder;

class Categories extends Component
{
    public $mainCategory;
    public $categoryGroup;
    public $subCategories;

    public function render()
    {
        $this->categoryGroup = SubCategoryGroup::withWhereHas('categories', function ($query) {
            
            $query->where('active', 1);
            $query->whereNull('parent_id');
            $query->orderBy('lft', 'asc');

        })->find($this->mainCategory->id);

        return view('livewire.post-ad.categories');
    }

    public function mount(SubCategoryGroup $mainCategory){

        $this->mainCategory = $mainCategory;

    }

    public function showSubCategories(string $subCategoriesId){

        if($this->mainCategory->id == 2 || $this->mainCategory->id == 5){
            
            $this->subCategories = Category::where('parent_id', $subCategoriesId)->whereIn('transaction_type' , ['rent'])->where('active', 1)->orderBy('lft', 'asc')->get();

        }else{

            $this->subCategories = Category::where('parent_id', $subCategoriesId)->whereIn('transaction_type', ['sell', 'both'])->where('active', 1)->orderBy('lft', 'asc')->get();
        }

    }
}
