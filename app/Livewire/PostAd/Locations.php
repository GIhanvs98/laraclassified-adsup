<?php

namespace App\Livewire\PostAd;

use App\Models\Category;
use App\Models\City;
use App\Models\SubAdmin2;
use App\Models\SubCategoryGroup;
use Livewire\Component;

class Locations extends Component
{
    public $mainCategory;
    public $category;

    public $districts;
    public $selectedDistrict;
    public $cities;

    public function render()
    {
        $this->districts = SubAdmin2::where('active', 1)->withWhereHas('cities', function ($query) { $query->orderBy('order', 'asc'); })->orderBy('order', 'asc')->get();

        return view('livewire.post-ad.locations');
    }

    public function mount(SubCategoryGroup $mainCategory, Category $category){

        $this->mainCategory = $mainCategory;

        $this->category = $category;

    }

    public function showCities(string $districtId){

        $this->selectedDistrict = SubAdmin2::where('active', 1)->find($districtId);

        $this->cities = $this->selectedDistrict->cities()->orderBy('order', 'asc')->get();

    }

    public function selectCity(string $cityId){

        // Adding the city id.
        session(['post-ad.cityId' => $cityId]);

        return redirect()->route('post-ad.details', ['mainCategory' => $this->mainCategory->id, 'category' => $this->category->id, 'location' => $cityId]);

    }
}
