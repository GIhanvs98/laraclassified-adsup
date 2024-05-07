@if(isset($field->options))
    @foreach($field->options as $key => $option)
        <label class="flex mt-1 w-fit">
            <input value="{{ $option->id }}" type="checkbox" name="{{ $field->id }}" @if(isset($category->fields)) @if(in_array($option->id, $category->fields[$field->id])) checked @endif @endif class="border mt-1 mb-1 p-2 cmfield" style="font-size: 14px;cursor: pointer;">
            <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">{{ ucfirst($option->value) }}</div>
        </label>
    @endforeach
@else
    <div class="flex text-xs text-gray-800 mt-1">No options.</div>
@endif
<input type="checkbox_multiple" hidden checkbox_multiple_name="{{ $field->id }}" class="field">
