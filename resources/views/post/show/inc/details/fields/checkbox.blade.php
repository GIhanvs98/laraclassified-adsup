<div class="col-12">
    <div class="row mx-0">
        <div class="col-12 mb-2 fw-bolder field-values-pl-0" style="width: fit-content;color: #707676;font-weight: 400 !important;">{{ ucfirst($fieldName) }}:</div>
        <div class="row field-values-pl-2 mb-2">
            @foreach($fieldValue as $valueItem)
                <div class="col-sm-4 col-6 py-2">
                    <i class="fa fa-check"></i> {{ isset($valueItem['value']) ? ucfirst($valueItem['value']) : ucfirst($valueItem) }}
                </div>
            @endforeach
        </div>
    </div>
</div>