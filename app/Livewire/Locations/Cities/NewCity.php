<?php

namespace App\Livewire\Locations\Cities;

use Livewire\Attributes\Rule;
use Livewire\Component;
use App\Models\SubAdmin2;
use App\Models\City;

class NewCity extends Component
{
    #[Rule('required|string')]
    public string $name = "";

    #[Rule('required|boolean')]
    public bool $status = false;

    #[Rule('required|numeric')]
    public string $longitude = "";

    #[Rule('required|numeric')]
    public string $latitude = "";
    
    #[Rule('required|min:0')]
    public string $population = "";

    #[Rule('required|min:0')]
    public string $order = "";

    #[Rule('required')]
    public string $feature_code = "";

    #[Rule('required|exists:subadmin2,code')]
    public string $districtId = "";

    public $districts;

    public $error_output;

    public function render()
    {
        return view('livewire.locations.cities.new-city');
    }

    public function mount(){

        $this->districts = SubAdmin2::where('active', 1)->orderBy('name', 'asc')->get();
    }

    public function save(){

        $this->validate(); 

        try {
                
            $city = new City;
            $city->name = $this->name;
            $city->country_code = "LK";
            $city->active = $this->status;
            $city->longitude = $this->longitude;
            $city->latitude = $this->latitude;
            $city->population = $this->population;
            $city->order = $this->order;
            $city->feature_class = "P";
            $city->feature_code = $this->feature_code;

            $district = SubAdmin2::find($this->districtId);

            $city->subadmin1_code = $district->subadmin1_code;

            $city->subadmin2_code = $district->code;

            $city->time_zone = "Asia/Colombo";
            $city->save();

        } catch (\Throwable $th) {

            $this->error_output = "Unexpected error occured!";

            // dd($th->getMessage());
        }

        return redirect()->route('admin.locations.cities');
    }
}
