@php 

$specialPostValues = ""; 

$unitField = App\Models\Field::with('unit')->find($fieldId);

@endphp

{{ ucfirst($fieldValue) }}

@if(isset($unitField->unit))

    @if(App\Models\PostValue::where('post_id', $post['id'])->whereBelongsTo($unitField->unit)->has('option')->exists())
        
        @php

            $postOption = App\Models\PostValue::where('post_id', $post)->whereBelongsTo($unitField->unit)->has('option')->first();

        @endphp

        @if(isset($postOption->option))

            @if(isset($postOption->option->value))

                {{ strtolower($postOption->option->value) }}

            @else

                {{ strtolower($unitField->name) }}
                
            @endif

        @else

            {{ strtolower($unitField->name) }}

        @endif

    @else

        {{ strtolower($unitField->name) }}

    @endif

@endif