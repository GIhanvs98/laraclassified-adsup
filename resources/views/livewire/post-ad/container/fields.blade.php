<div class="row" style="user-select: none;">

    <h4 class="text-lg" style="@if(isset($category->title_auto_generation_fields_order)) margin: 20px 0px 0px 0px; @else margin: 20px 0px; @endif padding-bottom: 0px;padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2);">
        Fill in the details
    </h4>

    @if(!isset($category->title_auto_generation_fields_order))
        @include('livewire.post-ad.fields.custom.title')
    @endif

    @if($mainCategory->id != 5 || $mainCategory->id != 6)
    
        @foreach($adFields as $key => $field)

            @switch($field->type)

                @case('text')
                    @include('livewire.post-ad.fields.input', ['type' => 'text'])
                @break

                @case('url')
                    @include('livewire.post-ad.fields.input', ['type' => 'url'])
                @break

                @case('number')
                    @include('livewire.post-ad.fields.input', ['type' => 'number'])
                @break

                @case('date')
                    @include('livewire.post-ad.fields.input', ['type' => 'date'])
                @break

                @case('date_range')
                    @include('livewire.post-ad.fields.input', ['type' => 'date_range'])
                @break

                @case('select')
                    @include('livewire.post-ad.fields.select')
                @break

                @case('video')
                    @include('livewire.post-ad.fields.video')
                @break

                @case('radio')
                    @include('livewire.post-ad.fields.radio')
                @break

                @case('checkbox')
                    @include('livewire.post-ad.fields.checkbox')
                @break

                @case('checkbox_multiple')
                    @include('livewire.post-ad.fields.checkbox')
                @break

                @default
                    @include('livewire.post-ad.fields.input', ['type' => 'text'])

            @endswitch
        
        @endforeach

    @endif

    @if($mainCategory->id != 5 || $mainCategory->id != 6)

        @include('livewire.post-ad.fields.custom.images')

    @endif

    @include('livewire.post-ad.fields.custom.description')

    @if($mainCategory->id != 5 || $mainCategory->id != 6)

        @include('livewire.post-ad.fields.custom.price')

    @endif

    <div class="input-group" style="margin-top: 20px;"><div style="border-bottom: 1px solid #d4ded9;padding-bottom: 10px; height: 5px; width: 100%;"></div></div>
    
</div>

<div class="row" style="user-select: none;">

    <h4 class="text-lg" style="margin: 20px 0px;padding-bottom: 0px;padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2);">
        User details
    </h4>

    @include('livewire.post-ad.fields.custom.name')

    @include('livewire.post-ad.fields.custom.email')

    @foreach($contact_numbers as $key => $contact_number)
        @include('livewire.post-ad.fields.custom.contact_number', [
            'title' => $contact_number['attribute_name'],
            'field' => $contact_number,
            'styles' => [
                'parent' => 'margin-top: 20px;',
            ],
            'classes' => [
                'label' => 'block mb-1 text-xs font-normal text-gray-500',
                'input' => 'border w-full mt-1 mb-1 p-2',
                'readonly' => 'border w-full mt-1 mb-1 p-2  bg-gray-100',
                'button' => 'text-white btn-primary focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-2.5 h-fit mb-1 ml-3',
            ],
        ])
    @endforeach

    @include('livewire.post-ad.fields.custom.add_contact_number_button')

</div>