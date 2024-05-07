<div class="col-12">
    <div class="row mx-0">
        <div class="col-6 fw-bolder field-values-pl-0">{{ ucfirst($fieldName) }}</div>
        <div class="col-6">
            <a class="btn btn-default" href="{{ $fieldValue }}" target="_blank">
                <i class="fas fa-paperclip"></i> {{ t('Download') }}
            </a>
        </div>
    </div>
</div>