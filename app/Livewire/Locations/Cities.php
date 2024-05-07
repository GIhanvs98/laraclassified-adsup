<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubAdmin2;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;

class Cities extends Component
{
    use WithPagination;

    public $districts;

    public string $search = "";

    public $citiesExists;

    public function render()
    {
        return view('livewire.locations.cities', [
            'cities' => City::with('subAdmin2')->where(function (Builder $query) {
                                    $query->orWhere('subadmin1_code', 'like', '%'.$this->search.'%')
                                            ->orWhere('subadmin2_code', 'like', '%'.$this->search.'%')
                                            ->orWhere('name', 'like', '%'.$this->search.'%');
                                })->orderBy('id', 'asc')->paginate(20),
                                
        ]);
    }

    public function mount(){

        $this->citiesExists = City::exists();

        $this->districts = SubAdmin2::where('active', 1)->get();
    }

    public function searchClear(){

        $this->search = "";
    }

    public function Status(City $city){

        $city->active = !$city->active;

        $city->save();
    }

    protected $listeners = ['change-district' => 'changeDistrict'];
 
    public function changeDistrict(SubAdmin2 $district, City $city)
    {
        $city->subadmin1_code = $district->subadmin1_code;

        $city->subadmin2_code = $district->code;

        $city->subAdmin2()->associate($district);

        $city->save();
    }

    public function deleteCity(City $city){

        try {
            
            $city->delete();
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
