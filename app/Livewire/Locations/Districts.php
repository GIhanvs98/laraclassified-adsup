<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubAdmin1;
use App\Models\SubAdmin2;
use Illuminate\Database\Eloquent\Builder;

class Districts extends Component
{
    use WithPagination;

    public $provinces;

    public string $search = "";

    public $districtsExists;

    public function render()
    {
        return view('livewire.locations.districts', [
            'districts' => SubAdmin2::with('subAdmin1')->where(function (Builder $query) {
                                    $query->orWhere('subadmin1_code', 'like', '%'.$this->search.'%')
                                            ->orWhere('code', 'like', '%'.$this->search.'%')
                                            ->orWhere('name', 'like', '%'.$this->search.'%');
                                })->orderBy('code', 'asc')->paginate(20),
                                
        ]);
    }

    public function mount(){

        $this->districtsExists = SubAdmin2::exists();

        $this->provinces = SubAdmin1::where('active', 1)->get();
    }

    public function searchClear(){

        $this->search = "";
    }

    public function Status(SubAdmin2 $district){

        $district->active = !$district->active;

        $district->save();
    }

    protected $listeners = ['change-province' => 'changeProvince'];
 
    public function changeProvince(SubAdmin1 $province, SubAdmin2 $district)
    {
        $district->code = $province->id.".31";

        $district->subAdmin1()->associate($province);

        $district->save();
    }
}
