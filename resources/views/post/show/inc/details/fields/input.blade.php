<div class="col-sm-6 col-12">
    <div class="row mx-0">
        <span class="fw-bolder field-values-pl-0" style="width: fit-content;color: #707676;font-weight: 400 !important;">{{ ucfirst($fieldName) }}:</span>
        <span style="width: fit-content;padding-left: 0px;">
            @if ($fieldType == 'url')
                <a href="{{ $fieldValue }}" target="_blank" rel="nofollow">{{ ucfirst($fieldValue) }}</a>
            @else
                @include('post.show.inc.details.field-units')
            @endif
        </span>
    </div>
</div>