<?php

namespace App\Livewire\Modal;

use Livewire\Component;
use App\Models\Category as CategoryModel;

class Category extends Component
{

    public $categories;

    public $selectedCategory;

    public $subCategories;

    public function render()
    {
        $this->categories = CategoryModel::where('active', 1)->whereNull('parent_id')->orderBy('lft', 'asc')->get();

        return view('livewire.modal.category');
    }

    public function showSubCategories(string $categoryId){

        $this->selectedCategory = CategoryModel::where('active', 1)->whereNull('parent_id')->find($categoryId);

        $this->subCategories = CategoryModel::where('parent_id', $categoryId)->where('active', 1)->orderBy('lft', 'asc')->get();
        
    }
}
