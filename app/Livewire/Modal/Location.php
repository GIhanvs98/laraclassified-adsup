<?php

namespace App\Livewire\Modal;

use Livewire\Component;
use App\Models\SubAdmin2;

class Location extends Component
{
    // Model data
    public $districts;

    public $selectedDistrict;

    public $cities;

    public function render()
    {
        // Loading districts 

        $this->districts = SubAdmin2::where('active', 1)->withWhereHas('cities', function ($query) { $query->orderBy('order', 'asc'); })->orderBy('order', 'asc')->get();

        return view('livewire.modal.location');
    }

    public function showCities(string $districtId){

        $this->selectedDistrict = SubAdmin2::where('active', 1)->find($districtId);

        $this->cities = $this->selectedDistrict->cities()->orderBy('order', 'asc')->get();

    }
}
