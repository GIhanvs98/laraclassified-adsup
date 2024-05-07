<input 
    name="{{ $field->id }}" 

    @if(isset($category->fields[$field->id])) 
        value="{{ $category->fields[$field->id] }}" 
    @endif

    @if(isset($type))
        
        @if($type=="date_range") 
            type="text"
        @else 
            type="{{ $type }}" 
        @endif 
        
        @if($type=="date_range" ) 
            placeholder="Date range, eg:- 01/01/2018 - 01/15/2018" 
        @else 
            placeholder="Enter {{ strtolower($field->name) }}" 
        @endif

        @if($type=="date_range" ) 
            class="date-range-picker border w-full mt-1 mb-1 p-2 field text-sm" 
        @else 
            class="border w-full mt-1 mb-1 p-2 field text-sm" 
        @endif

    @else
        type="text"
        placeholder="Enter {{ strtolower($field->name) }}"
        class="border w-full mt-1 mb-1 p-2 field text-sm"
    @endif
>