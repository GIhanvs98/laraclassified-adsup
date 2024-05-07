<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use App\Models\SubAdmin2;

class DistrictsReorder extends Component
{
    public $districtsExists;

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

        return view('livewire.locations.districts-reorder', [
            'districts' => SubAdmin2::with('subAdmin1')->orderBy($orderBy, $orderPattern)->get(), 
        ]);
    }

    public function mount(string $order){

        $this->order = $order;

        $this->districtsExists = SubAdmin2::exists();
    }

    protected $listeners = ['sort' => 'sortDistricts'];
 
    public function sortDistricts($elements)
    {
        $elementsArray = json_decode($elements);
        
        foreach ($elementsArray as $key => $elementId) {
            
            SubAdmin2::find($elementId)->update(['order' => $key+1]);

        }
    }
}
