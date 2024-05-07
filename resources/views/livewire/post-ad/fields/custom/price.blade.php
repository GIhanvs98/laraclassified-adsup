@php
    $priceValueStyle="border-radius: 5px;";

    $priceUnitStyle="border-radius: 0 5px 5px 0;";

    $priceUnitVisible = (($mainCategory->id == 1 && $category->id == 10) || ($mainCategory->id == 2 && $category->id == 10) || ($mainCategory->id == 2 && ( $category->id == 11 || $category->id == 12 || $category->id == 1114 )) || ($mainCategory->id == 2 && $category->id == 1115) || ($mainCategory->id == 2 && $category->id == 1116));

    if($priceUnitVisible){
        $priceValueStyle = "border-radius: 5px 0 0 5px;";
    }

@endphp

<div class="input-group" style="margin-top: 20px;">
    <div class="text-xs text-gray-500 flex">
        @if ($mainCategory->id == 2) Rent @elseif($mainCategory->id == 3 || $mainCategory->id == 4) Salary @else Price @endif
    </div>
    <div class="flex w-full items-center">

        <input wire:model="price.value" @if(!isset($price['value'])) @endif type="number" placeholder="@if ($mainCategory->id == 2) Rent price @elseif($mainCategory->id == 3 || $mainCategory->id == 4) Salary @else Price @endif" class="border w-80 mt-1 mb-1 p-2 " style="font-size: 14px; {{ $priceValueStyle }}">

        @if($mainCategory->id == 1 && $category->id == 10)

            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.1_10') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            
        @elseif($mainCategory->id == 2 && $category->id == 10)

            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.2_10') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            
        @elseif($mainCategory->id == 2 && ( $category->id == 11 || $category->id == 12 || $category->id == 1114))

            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.2_11_12_1114') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            
        @elseif($mainCategory->id == 2 && $category->id == 1115)

            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.2_1115') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            
        @elseif($mainCategory->id == 2 && $category->id == 1116)

            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.2_1116') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>

        @elseif($mainCategory->id == 3 || $mainCategory->id == 4)
            
            <select wire:model="price.unit" class="border w-fit mt-1 mb-1 p-2 cursor-pointer" required style="font-size: 14px;padding-right: 30px !important; border-left: 0px !important; {{ $priceUnitStyle }}">
                <option value="" disabled>Select the unit</option>
                @foreach ( config('fields.price-unit.3_4') as $option )
                    <option value="{{ str_slug($option) }}">{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        @else
            <!-- Default:total -->
        @endif
        
        <div class="input-group block w-fit px-4 flex items-center">
            <label class="flex m-0" style="cursor: pointer;">
                <input wire:model="negotiable" type="checkbox" class="border mt-1 mb-0 p-2" style="font-size: 14px;cursor: pointer; border-radius: 5px;">
                <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">Negotiable</div>
            </label>
        </div>

    </div>

    @error('price.value') <div class="text-xs text-red-600">{{ $message }}</div> @enderror 
    @error("negotiable") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
    @if($priceUnitVisible)
        @error("price.unit") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
    @endif
</div>