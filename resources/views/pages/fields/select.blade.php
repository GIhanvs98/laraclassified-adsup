<select class="border w-full mt-1 mb-1 p-2 cursor-pointer field text-sm" name="{{ $field->id }}">
    <option value="" disabled 
        @if(isset($category->fields))

            @if(!isset($category->fields[$field->id]) || empty($category->fields[$field->id]) ) 
                selected 
            @endif

        @else
            selected
        @endif 
    
    >Select the {{ strtolower($field->name) }}</option>

    @isset($field->options)
        @foreach($field->options as $key => $option)
            <option value="{{ $option->id }}" @if(isset($category->fields)) @if($category->fields[$field->id] == $option->id) selected @endif @endif>{{ ucfirst($option->value) }}</option>
        @endforeach
    @endisset
</select>