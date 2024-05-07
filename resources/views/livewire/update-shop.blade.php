<form name="details" class="form-horizontal" wire:submit.prevent="save">
    
    {{-- Shop --}}
    {{--<div class="row mb-3 required">
        <label class="col-md-3 col-form-label" for="shop-title">Title</label>
        <div class="col-md-9 col-lg-8 col-xl-6"style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
            <input id="shop-title" wire:model.blur="title" name="shop-title" type="text" class="form-control" placeholder="Shop Title">
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>
    </div>--}}

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label" for="shop-description">Description</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
            <textarea id="shop-description" wire:model.blur="shopDescription" name="shop-description" rows="5" class="form-control" placeholder="Shop Description" width="100%" style="height: 150px;"></textarea>
            @error('shopDescription') <span class="error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label" for="shop-address">Address</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
            <input id="shop-address" wire:model.blur="shopAddress" name="shop-address" type="address" class="form-control" placeholder="Shop Address">
            @error('shopAddress') <span class="error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label" for="wallpaperImage">Shop Wallpaper</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
            <div id="image-preview" class="max-w-sm p-6 bg-gray-100 border-dashed border-2 border-gray-400 rounded-lg items-center mx-auto text-center cursor-pointer">
                <input wire:model.blur="wallpaperImage" id="wallpaper-image" name="wallpaper-image"  type="file" class="hidden" accept="image/*" />
                <label for="wallpaper-image" class="cursor-pointer">
                    @if ($wallpaperImage or isset($wallpaperUrl) )
                        <img wire:loading.remove src="{{ (isset($wallpaperUrl)) ? $wallpaperUrl : $wallpaperImage->temporaryUrl() }}" class="max-h-48 rounded-lg mx-auto" alt="Image preview" />
                        <div wire:loading wire:target="wallpaperImage">
                            <div role="status">
                                <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 mx-auto mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-700">Upload picture</h5>
                        <p class="font-normal text-sm text-gray-400 md:px-6">Choose photo size should be less than <b class="text-gray-600">5mb</b></p>
                        <p class="font-normal text-sm text-gray-400 md:px-6">and should be in <b class="text-gray-600">JPG, JPE, JPEG or PNG</b> format.</p>
                        <span id="filename" class="text-gray-500 bg-gray-200 z-50"></span>
                    @endif
                </label>
            </div>
            @error('wallpaperImage') <span class="error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row required">
        <label class="col-md-3 col-form-label"><h5>Shop open hours</h5></label>
    </div>

    <!-- Monday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Monday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="mondayFrom" id="monday-from" name="monday-from" type="time" class="form-control" placeholder="From">
                    @error('mondayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="mondayTo" id="monday-to" name="monday-to" type="time" class="form-control" placeholder="TO">
                    @error('mondayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Tuesday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Tuesday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="tuesdayFrom" id="tuesday-from" name="tuesday-from" type="time" class="form-control" placeholder="From">
                    @error('tuesdayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="tuesdayTo" id="tuesday-to" name="tuesday-to" type="time" class="form-control" placeholder="TO">
                    @error('tuesdayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Wednesday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Wednesday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="wednesdayFrom" id="wednesday-from" name="wednesday-from" type="time" class="form-control" placeholder="From">
                    @error('wednesdayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="wednesdayTo" id="wednesday-to" name="wednesday-to" type="time" class="form-control" placeholder="TO">
                    @error('wednesdayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Thursday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Thursday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="thursdayFrom" id="thursday-from" name="thursday-from" type="time" class="form-control" placeholder="From">
                    @error('thursdayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="thursdayTo" id="thursday-to" name="thursday-to" type="time" class="form-control" placeholder="TO">
                    @error('thursdayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Friday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Friday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="fridayFrom" id="friday-from" name="friday-from" type="time" class="form-control" placeholder="From">
                    @error('fridayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="fridayTo" id="friday-to" name="friday-to" type="time" class="form-control" placeholder="TO">
                    @error('fridayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Saturday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Saturday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="saturdayFrom" id="saturday-from" name="saturday-from" type="time" class="form-control" placeholder="From">
                    @error('saturdayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="saturdayTo" id="saturday-to" name="saturday-to" type="time" class="form-control" placeholder="TO">
                    @error('saturdayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Sunday -->
    <div class="row required">
        <label class="col-md-3 col-form-label form-time-slot-label" for="wallpaperImage">Sunday</label>
        <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px); padding-bottom: calc(0.375rem + 1px);">
            <div class="row">
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="sundayFrom" id="sunday-from" name="sunday-from" type="time" class="form-control" placeholder="From">
                    @error('sundayFrom') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 col-12" style="padding-bottom: calc(0.375rem + 1px);">
                    <input wire:model.blur="sundayTo" id="sunday-to" name="sunday-to" type="time" class="form-control" placeholder="TO">
                    @error('sundayTo') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- button --}}
    <div class="row">
        <div class="offset-md-3 col-md-9">
            <button type="submit" class="btn btn-primary flex">
                <div class="w-fit" wire:loading.inline-block wire:target="save">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                    </svg>
                </div>
                {{ t('Save') }}
            </button>
        </div>
    </div>

    <style>
        .mr-3{
            margin-right: 0.75rem
        }

        .inline{
            display: inline
        }

        .h-4{
            height: 1rem
        }

        .w-4{
            width: 1rem
        }

        @keyframes spin{
            to{
                transform: rotate(360deg)
            }
        }

        .animate-spin{
            animation: spin 1s linear infinite
        }

        .text-white{
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity))
        }
</style>


</form>