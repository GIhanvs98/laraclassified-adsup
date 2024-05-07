<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use App\Models\SubAdmin2;
use App\Models\City;

class CitiesReorder extends Component
{
    public $citiesExists;

    public $district;

    public string $order;

    public function render()
    {
        if($this->order == "a-z"){

            $orderBy = 'name';
            $orderPattern = 'asc';

        }else if($this->order == "z-a"){

            $orderBy = 'name';
            $orderPattern = 'desc';

        }else if($this->order == "1-9"){

            $orderBy = 'order';
            $orderPattern = 'asc';

        }else if($this->order == "9-1"){

            $orderBy = 'order';
            $orderPattern = 'desc';

        }else{

            $orderBy = 'order';
            $orderPattern = 'asc';

        }

        return view('livewire.locations.cities-reorder', [
            'cities' => City::where('subadmin2_code', $this->district->code)->orderBy($orderBy, $orderPattern)->get(), 
        ]);
    }

    public function mount(string $order, SubAdmin2 $district){

        $this->order = $order;

        $this->district = $district;

        $this->citiesExists = City::where('subadmin2_code', $this->district->code)->exists();
    }

    protected $listeners = ['sort' => 'sortCities'];
 
    public function sortCities($elements)
    {
        $elementsArray = json_decode($elements);
        
        foreach ($elementsArray as $key => $elementId) {
            
            City::find($elementId)->update(['order' => $key+1]);

        }
    }
}
