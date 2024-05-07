<div class="col-sm-6 col-12">
    <div class="row mx-0">
        <span class="fw-bolder field-values-pl-0" style="width: fit-content;color: #707676;font-weight: 400 !important;">{{ ucfirst($fieldName) }}:</span>
        <span style="width: fit-content;padding-left: 0px;">
            <span style="width: fit-content;padding-left: 0px;">
                @foreach ( $fieldValue as $fieldSelectedValue )
                    {{ ucfirst($fieldSelectedValue['value']) }}
                @endforeach
            </span>
        </span>
    </div>
</div>
