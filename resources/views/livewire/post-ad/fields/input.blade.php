<div class="input-group w-full" wire:key="fields.{{ $field->id }}_input" style="margin-top: 20px;">
    <div class="flex w-full">

        <div class="w-full col">
            <div class="text-xs text-gray-500 flex">{{ $field->name }} @if(!$field->required) (optional) @endif </div>
            <input @if($type == "date_range") type="text" @else type="{{ $type }}" @endif wire:model.blur="fields.{{ $field->id }}" value="{{ $field->default_value }}" @if($type == "date_range") placeholder="Date range, eg:- 01/01/2018 - 01/15/2018" @else placeholder="{{ $field->name }}" @endif maxlength="{{ $field->max }}" @if($field->required) required @endif @if($type == "date_range") class="date-range-picker border w-full mt-1 mb-1 p-2" @else class="border w-full mt-1 mb-1 p-2" @endif style="font-size: 14px; @if(isset($field->unit)) border-radius: 5px 0 0 5px; @else border-radius: 5px; @endif">
        </div>

        @isset($field->unit)

            <div class="col-3"><!-- w-fit sm:w-full -->
                <div class="text-xs text-gray-500 flex">unit</div>
                <select wire:model.blur="fields.{{ $field->unit->id }}" @if(!isset($price['value'])) wire:init="$set('fields.{{ $field->unit->id }}', null)" @endif class="border w-full mt-1 mb-1 p-2 cursor-pointer" @if($field->unit->required) required @endif style="font-size: 14px; border-left: 0px !important;padding-right: 30px !important; @if(isset($field->unit)) border-radius: 0 5px 5px 0; @else border-radius: 5px; @endif">

                    <option value="" disabled>Select the unit&nbsp;</option>

                    @isset($field->unit->options)
                        @foreach($field->unit->options as $key => $option)
                            <option wire:key="fields.{{ $field->id }}_input_{{ $field->unit->id }}_unit_{{ $key }}_option" value="{{ $option->id }}">{{ ucfirst($option->value) }}</option>
                        @endforeach
                    @endisset

                </select>
            </div>
        @endisset

    </div>

    @error("fields.$field->id") <div class="text-xs text-red-600">{{ $message }}</div> @enderror

    @isset($field->unit)
        @error("fields.".$field->unit->id) <div class="text-xs text-red-600"> &nbsp;{{ $message }}</div> @enderror 
    @endisset
    
</div>