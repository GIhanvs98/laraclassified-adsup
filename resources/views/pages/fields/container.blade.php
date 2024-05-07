@if(isset($adFields))

    @foreach($adFields as $key => $field)

        @if ($field->type == 'select' || $field->type == 'radio' || $field->type == 'checkbox' ||  $field->type == 'checkbox_multiple')
            
            @include('pages.fields.accordition', ['fieldName' => $field->type])

        @elseif ($field->type == 'text' || $field->type == 'number' || $field->type == 'url' ||  $field->type == 'date' || $field->type == 'date_range')
            
            @include('pages.fields.accordition', ['fieldName' => 'input', 'type' => $field->type])

        @else

            @include('pages.fields.accordition', ['fieldName' => 'input', 'type' => 'text'])

        @endif

    @endforeach

@endif
