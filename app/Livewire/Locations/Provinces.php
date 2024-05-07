<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubAdmin1;
use Illuminate\Database\Eloquent\Builder;

class Provinces extends Component
{
    use WithPagination;

    public string $search = "";

    public $provincesExists;

    public function render()
    {
        return view('livewire.locations.provinces', [
            'provinces' => SubAdmin1::where(function (Builder $query) {
                                    $query->orWhere('code', 'like', '%'.$this->search.'%')
                                            ->orWhere('name', 'like', '%'.$this->search.'%');
                                })->orderBy('code', 'asc')->paginate(20),
                                
        ]);
    }

    public function mount(){

        $this->provincesExists = SubAdmin1::exists();
    }

    public function searchClear(){

        $this->search = "";
    }

    public function Status(SubAdmin1 $province){

        $province->active = !$province->active;

        $province->save();
    }
}
