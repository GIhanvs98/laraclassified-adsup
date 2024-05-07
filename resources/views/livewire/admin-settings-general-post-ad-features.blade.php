<div>

    <p class="mb-3 text-gray-400">All the below settings will be added before saving the ads. (Enabled settings will be checked)</p>

    <div class="accordion accordion-flush" id="accordionFlushExample">

        <div class="accordion-item" style="background-color: white;">
            <h2 class="accordion-header" id="flush-heading-image-compression">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-image-compression" aria-expanded="false" aria-controls="flush-collapse-image-compression">
                <div>Image compression</div>
                <div class="ml-2">
                    @if($compress_images)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </button>
            </h2>
            <div id="flush-collapse-image-compression" class="accordion-collapse collapse" aria-labelledby="flush-heading-image-compression" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">

                <div class="form-check mb-3">
                    <input class="form-check-input border" type="checkbox" wire:model.live="compress_images" id="compress_images">
                    <label class="form-check-label cursor-pointer" for="compress_images">
                        Allow image compression
                    </label>
                    <div>
                        @error('compress_images') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                @include('livewire.inc.admin-settings.user-ad-features-table', ['setting' => 'compress_images'])

            </div>
            </div>
        </div>

        <div class="accordion-item" style="background-color: white;">
            <h2 class="accordion-header" id="flush-heading-image-watermark">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-image-watermark" aria-expanded="false" aria-controls="flush-collapse-image-watermark">
                <div>Image watermark</div>
                <div class="ml-2">
                    @if($add_watermark)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </button>
            </h2>
            <div id="flush-collapse-image-watermark" class="accordion-collapse collapse" aria-labelledby="flush-heading-image-watermark" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">

                <div class="form-check mb-3">
                    <input class="form-check-input border" type="checkbox" wire:model.live="add_watermark" id="add_watermark">
                    <label class="form-check-label cursor-pointer" for="add_watermark">
                        Allow image watermark
                    </label>
                    <div>
                        @error('add_watermark') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                @include('livewire.inc.admin-settings.user-ad-features-table', ['setting' => 'watermark_images'])

            </div>
            </div>
        </div>

        <div class="accordion-item" style="background-color: white;">
            <h2 class="accordion-header" id="flush-heading-otp-verification">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-otp-verification" aria-expanded="false" aria-controls="flush-collapse-otp-verification">
                <div>OTP verification</div>
                <div class="ml-2">
                    @if($otp_verification)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </button>
            </h2>
            <div id="flush-collapse-otp-verification" class="accordion-collapse collapse" aria-labelledby="flush-heading-otp-verification" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                            
                <div class="form-check mb-3">
                    <input class="form-check-input border" type="checkbox" wire:model.live="otp_verification" id="otp_verification">
                    <label class="form-check-label cursor-pointer" for="otp_verification">
                        Allow otp verification
                    </label>
                    <div>
                        @error('otp_verification') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                @include('livewire.inc.admin-settings.user-ad-features-table', ['setting' => 'otp_verify'])

            </div>
            </div>
        </div>
        
        <div class="accordion-item" style="background-color: white;">
            <h2 class="accordion-header" id="flush-heading-guest-ads">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-guest-ads" aria-expanded="false" aria-controls="flush-collapse-guest-ads">
                <div>Guest ads</div>
                <div class="ml-2">
                    @if($guest_ads)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </button>
            </h2>
            <div id="flush-collapse-guest-ads" class="accordion-collapse collapse" aria-labelledby="flush-heading-guest-ads" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">

                <div class="form-check">
                    <input class="form-check-input border" type="checkbox" wire:model.live="guest_ads" id="guest_ads">
                    <label class="form-check-label cursor-pointer" for="guest_ads">
                        Allow guest ads
                    </label>
                    <div>
                        @error('guest_ads') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>
            </div>
        </div>

    </div>

    <div class="mt-3">
        <button type="button" wire:click="resetDefault" class="btn-sm btn-secondary">Reset Default</button>
    </div>

    <div class="mt-3">
        @if($successfull)
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Changes saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($error)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ $error_message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    {{-- CRUD Add Search Keyword modal --}}
    <div id="userSettings" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-[60] hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0" wire:ignore.self>
        @if($modal['state'] == "new" || ($modal['state'] == "edit"))
            <div class="relative max-h-full w-full max-w-2xl">
                <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow w-full">
                    <!-- Modal header -->
                    <div class="items-start justify-between rounded-t border-b p-5 flex justify-between">
                        <h3 class="block text-xl font-semibold text-gray-900 lg:text-2xl">
                            @if($modal['state'] == "new")
                                Add custom setting to user
                            @elseif($modal['state'] == "edit")
                                Edit user setting
                            @endif
                        </h3>
                        <button wire:click="$dispatch('hide-modal')" type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900">
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-6 p-5">

                        <div class="block mb-4">
                            <label for="category" class="block mb-2 text-sm font-medium text-gray-900 ">Setting</label>
                            <select class="form-select" wire:model="modal.setting" @if($modal['setting']) disabled @endif>
                                <option selected disabled value="">Select the setting</option>
                                <option value="compress_images">Compress images</option>
                                <option value="watermark_images">Watermark images</option>
                                <option value="otp_verify">OTP verify</option>
                            </select>
                            <div class="text-red-600 text-xs mt-1">@error('modal.setting') {{ $message }} @enderror</div>   
                        </div>

                        <div class="block mb-4">
                            <label for="category" class="block mb-2 text-sm font-medium text-gray-900 ">User</label>
                            <select class="form-select" wire:model="modal.values.user" @if($modal['state'] == "edit") disabled @endif>
                                <option selected disabled value="">Select the user</option>
                                @foreach($modal['users'] as $key => $user)
                                    <option value="{{ $user->id }}">{{ $user->email }} - {{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-red-600 text-xs mt-1">@error('modal.values.user') {{ $message }} @enderror</div>   
                        </div>

                        <div class="block mb-2">
                            <div class="flex">
                                <input id="default-checkbox" type="checkbox" wire:model="modal.values.setting" class="cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="default-checkbox" class="cursor-pointer ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Allow setting</label> 
                            </div>
                            <div class="text-red-600 text-xs mt-1">@error('modal.values.setting') {{ $message }} @enderror</div>   
                        </div>

                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center space-x-2 rtl:space-x-reverse pt-0 rounded-b p-5">
                        <!-- Start condition -->
                        <button wire:click="submit" class="btn btn-md btn-secondary flex items-center min-w-fit max-w-fit mb-1">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="relative max-h-full w-fit max-w-2xl">
                <!-- Modal content -->
                <div class="relative rounded-full text-center w-fit p-4">
                    <div role="status">
                        <svg aria-hidden="true" class="inline w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
        <script type="module">

            document.addEventListener('livewire:initialized', () => {

                // set the view revisions modal
                const targetEl = document.getElementById('userSettings');

                // options with default values
                const options = {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses:
                        'bg-gray-900/50 fixed inset-0 z-50',
                    closable: true
                };

                const modal = new Modal(targetEl, options);

                $wire.on('show-modal', () => {
                    modal.show();
                });

                $wire.on('hide-modal', () => {
                    modal.hide();
                    $wire.$set('modal.state', null);
                });

            });

        </script>
    @endscript

</div>
