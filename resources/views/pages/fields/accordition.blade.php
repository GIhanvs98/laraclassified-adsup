<h2 id="accordion-flush-heading-field-{{ $key }}" style="padding-bottom: 0px;">
    <button type="button" class="flex w-full items-center justify-between border-t border-gray-200 py-3 text-left font-medium text-gray-500 text-xs" data-accordion-target="#accordion-flush-body-field-{{ $key }}" aria-expanded="false" aria-controls="accordion-flush-body-3">
        <span>{{ $field->name }}</span>
        <svg data-accordion-icon class="h-2 w-2 shrink-0 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
        </svg>
    </button>
</h2>
<div id="accordion-flush-body-field-{{ $key }}" @if(!isset($category->fields[$field->id]) || empty(($category->fields[$field->id])) ) class="hidden" @endif aria-labelledby="accordion-flush-heading-field-{{ $key }}">
    <div class="border-gray-200 pb-3 pt-0">
        <div class="input-group block">

            @if(isset($inputType))
                
                @include('pages.fields.'.$fieldName, ['type' => $type])

            @else
                
                @include('pages.fields.'.$fieldName)

            @endif

        </div>
    </div>
</div>
