<div wire:key="fields.{{ $field->id }}_checkbox" class="input-group block" style="margin-top: 20px;">
    <div class="text-xs text-gray-500 flex">{{ $field->name }} @if(!$field->required) (optional) @endif </div>
    <div class="row pt-2">

        @if(isset($field->options))
            @foreach($field->options as $key => $option)

                <label wire:key="fields.{{ $field->id }}_checkbox_{{ $key }}_subCheckbox" class="flex mt-1 col-12 col-md-6 col-lg-4 col-xl-3 pl-0" style="padding-right: 25px;">
                    <input wire:model.blur="fields.{{ $field->id }}" value="{{ $option->id }}" type="checkbox" @if($field->required) required @endif class="border mt-1 mb-1 p-2" style="font-size: 14px;cursor: pointer; border-radius: 5px;">
                    <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">{{ $option->value }}</div>
                </label>

            @endforeach
        @else
            <div class="flex text-xs text-gray-800 mt-1">No options.</div>
        @endif

    </div>
    @error("fields.$field->id") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
</div>
