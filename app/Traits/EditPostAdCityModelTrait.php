<?php

namespace App\Traits;
use App\Models\City;
use App\Models\SubAdmin2;

trait EditPostAdCityModelTrait
{
    public function showCities(string $districtId)
    {
        $this->selectedDistrict = SubAdmin2::where('active', 1)->find($districtId);

        $this->cities = $this->selectedDistrict->cities()->orderBy('order', 'asc')->get();
    }

    public function selectCity(string $cityId)
    {
        $this->city = City::whereId($cityId)->where('active', 1)->first();

        # Adding the city id.
        session(['post-ad.cityId' => $cityId]);

        $this->dispatch('changeLocation');
    }
}