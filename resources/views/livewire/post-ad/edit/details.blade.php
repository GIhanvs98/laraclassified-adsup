<div>
    <form>

        <h3 class="text-2xl" style="margin: 0 0 15px;">Edit the Ad `{{ $post->title }}`</h3>

        <div class="default-values flex" style="padding: 15px 0px;justify-content: space-between;border-bottom: 1px solid #d4ded9;border-top: 1px solid #d4ded9;padding-left: 1rem !important;padding-right: 1rem !important;">
            <h5 style="padding: 0px;"><strong>City:</strong> {{ $city->name }}</h5>
            <a style="padding-left: 10px;" data-modal-target="changeLocation" data-modal-toggle="changeLocation">Change</a>
        </div>

        @include('livewire.post-ad.container.fields')

        <div class="text-center" style="margin-top: 20px;">

            <a type="reset" class="btn btn-default btn-lg mr-2" href="{{ route('posts.edit', ['id' => $post->id]) }}">Get Last Chnages</a>

            <button type="button" class="btn btn-primary btn-lg" wire:loading.remove wire:target="save" id="save-post">Save Changes</button>

            @include('livewire.post-ad.fields.custom.loading_button')

        </div>

        @include('livewire.post-ad.fields.custom.error_output')

    </form>

    <!-- Change location modal -->
    @include('livewire.post-ad.modals.location')

    @include('livewire.post-ad.inline.style')

    @include('livewire.post-ad.inline.script')

</div>
