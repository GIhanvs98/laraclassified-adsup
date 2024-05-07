<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\SubAdmin1;
use App\Models\SubAdmin2;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces(){
        $count = SubAdmin1::count();

        return view('admin.dashboard.locations.provinces', ['count' => $count]);
    }

    public function districts(){ 
        $count = SubAdmin2::count();

        return view('admin.dashboard.locations.districts', ['count' => $count]);
    }

    public function reorderDistricts(Request $request){

        $order = $request->query('order');

        if(!isset($order)){

            $order = "normal";
        }

        return view('admin.dashboard.locations.districts-reorder', ['order' => $order]);
    }

    public function cities(){   
        $count = City::count();

        return view('admin.dashboard.locations.cities', ['count' => $count]);
    }

    public function reorderCities(Request $request, string $districtId){

        if(SubAdmin2::where('code', $districtId)->doesntExist()){

            return abort(404);
        }

        $order = $request->query('order');

        if(!isset($order)){

            $order = "normal";
        }

        $district = SubAdmin2::find($districtId);

        return view('admin.dashboard.locations.cities-reorder', ['district' => $district, 'order' => $order]);
    }

    public function newCity(){

        return view('admin.dashboard.locations.cities.new');
    }

    public function editCity(string $cityId){

        if(City::whereId($cityId)->doesntExist()){

            return abort(404);
        }

        $city = City::find($cityId);

        return view('admin.dashboard.locations.cities.edit', ['city' => $city]);
    }
}
