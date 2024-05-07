@php

    $paddingCode = "";
    
    if(isset($padding)){
        
        if($padding){

            $paddingCode = "padding-left: 5px;";
        }
    }

@endphp

<span class="info-row" style="color: #2b2b2b;{{ $paddingCode }}">

    @php $specialPostValues = ""; @endphp

    @isset($post->postValues)

        @foreach ( $post->postValues as $postValue )

            @if(isset($postValue->field) && ($postValue->field->is_search_item_visible == 1))

                @php $specialPostValues = ucfirst($postValue->value).' '; @endphp

                @if(isset($postValue->field->unit))

                    @if(App\Models\PostValue::whereBelongsTo($post)->whereBelongsTo($postValue->field->unit)->has('option')->exists())
                        
                        @php

                            $postOption = App\Models\PostValue::whereBelongsTo($post)->whereBelongsTo($postValue->field->unit)->has('option')->first();

                        @endphp

                        @if(isset($postOption->option))

                            @if(isset($postOption->option->value))

                                @php $specialPostValues .= strtolower($postOption->option->value).', '; @endphp

                            @else

                                @php $specialPostValues .= strtolower($postValue->field->name).', '; @endphp
                                
                            @endif

                        @else

                            @php $specialPostValues .= strtolower($postValue->field->name).', '; @endphp

                        @endif

                    @else

                        @php $specialPostValues .= strtolower($postValue->field->name).', '; @endphp

                    @endif

                @else

                    @php $specialPostValues .= strtolower($postValue->field->name).', '; @endphp

                @endif
                
            @endif
            
        @endforeach

        {{ substr_replace($specialPostValues,"", -2) }}
        
    @endisset

</span>