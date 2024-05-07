<div wire:key="fields.{{ $field->id }}_select" class="input-group" style="margin-top: 20px;">
    <div class="text-xs text-gray-500 flex">{{ $field->name }} @if(!$field->required) (optional) @endif </div>
    <select wire:model.blur="fields.{{ $field->id }}" class="border w-full mt-1 mb-1 p-2 cursor-pointer" @if($field->required) required @endif style="font-size: 14px;padding-right: 30px !important; border-radius: 5px;">

        <option value="" disabled>Select the {{ strtolower($field->name) }}</option>

        @isset($field->options)
            @foreach($field->options as $key => $option)
                <option wire:key="fields.{{ $field->id }}_select_{{ $key }}_option" value="{{ $option->id }}">{{ ucfirst($option->value) }}</option>
            @endforeach
        @endisset

    </select>
    @error("fields.$field->id") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
</div>