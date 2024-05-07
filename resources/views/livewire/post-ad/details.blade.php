<form>

    <h3 class="text-2xl" style="margin: 0 0 15px;">{{ $mainCategory->name }}</h3>

    <div class="default-values flex" style="padding: 15px 0px;justify-content: space-between;border-bottom: 1px solid #d4ded9;border-top: 1px solid #d4ded9;padding-left: 1rem !important;padding-right: 1rem !important;">
        <h5 style="padding: 0px;"><strong>Category:</strong> {{ $category->name }}</h5>
        <a style="padding-left: 10px;" href="{{ ($mainCategory->id == 3 || $mainCategory->id == 4) ? route('post-ad.index') : route('post-ad.main-category', ['mainCategory' => $mainCategory->id]) }}">Change</a>
    </div>

    <div class="default-values flex" style="padding: 15px 0px;justify-content: space-between;border-bottom: 1px solid #d4ded9;padding-left: 1rem !important;padding-right: 1rem !important;">
        <h5 style="padding: 0px;"><strong>City:</strong> {{ $city->name }}</h5>
        <a style="padding-left: 10px;" href="{{ route('post-ad.location', ['mainCategory' => $mainCategory->id, 'category' => $category->id, 'l' => 'clear']) }}">Change</a>
    </div>

    @include('livewire.post-ad.container.fields')

    @if(!$user_acepted_terms)

        @include('livewire.post-ad.fields.custom.accept_terms')
        
    @endif

    <div class="text-center" style="margin-top: 20px;">

        <button type="reset" class="btn btn-default btn-lg mr-2">Clear Ad</button>

        <button type="button" class="btn btn-primary btn-lg" wire:loading.remove wire:target="save" id="save-post">Post Ad</button>

        @include('livewire.post-ad.fields.custom.loading_button')

    </div>

    @include('livewire.post-ad.fields.custom.error_output')

    @include('livewire.post-ad.inline.style')

    @include('livewire.post-ad.inline.script')

</form>
