@if(isset($field->options))
        @foreach($field->options as $key => $option)
            <label class="flex mt-1 w-fit">
                <input value="{{ $option->id }}" type="radio" name="{{ $field->id }}" @if(isset($category->fields)) @if($category->fields[$field->id] == $option->id) checked @endif @endif class="border mt-1 mb-1 p-2 field" style="font-size: 14px;cursor: pointer;">
                <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">{{ ucfirst($option->value) }}</div>
            </label>
        @endforeach
@else
    <div class="flex text-xs text-gray-800 mt-1">No options.</div>
@endif
