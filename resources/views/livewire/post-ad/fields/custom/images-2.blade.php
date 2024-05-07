<div class="input-group" style="margin-top: 20px;">
  <div class="text-xs text-gray-500">Add up to 5 photos</div>
  <div class="mt-4 block">
    
    @for($i = 0; $i < $imagesLimit; $i++)

        @if ($images)

            {{-- new/edit :- when a new image selected --}}

            <!-- Image preview instance -->
            <label wire:key="{{ $i }}" style="background-image: url({{ $image->temporaryUrl() }});" class="relative ml-1 mb-1 inline-flex h-24 w-24 items-center justify-center bg-cover bg-center bg-no-repeat not-sortable cursor-pointer">
                
                <div class="block invisible">
                    <div class="align-center mx-auto flex h-auto w-10 fill-blue-700">
                        <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e"><path d="M19.4 17.44l-2.1-6.76-2.45 1.5-1.5-3.7-4 6.45-1.15-1.71-4.08 4.22 15.18-.05zM7.47 9.53A1.44 1.44 0 1 1 6 8.05a1.46 1.46 0 0 1 1.47 1.48zM2.93 5.08h18.14v13.84H2.93zM1.5 20.4h21V3.6h-21z" fill-rule="evenodd"></path></svg>
                    </div>
                    <div class="text-sm text-blue-700">Add a photo</div>
                </div>

                <button class="absolute top-1 right-1">
                    <svg width="18" height="18" viewBox="0 0 18 18" class="svg-wrapper--8ky9e"><g fill="none" fill-rule="evenodd"><path fill-opacity="0" fill="red" d="M0 0h18v18H0z"></path><g transform="translate(1 1)"><circle fill="#D95E46" cx="8" cy="8" r="8"></circle><rect fill="#FFF" x="4" y="7" width="8" height="2" rx="0.5"></rect></g></g></svg>
                </button>

                <input wire:model="images.{{ $i }}" type="file" class="hidden" />
            </label>

        @elseif (isset($uploadedImages))

            {{-- edit :- when a image uploaded to server --}}

            <!-- Image preview instance -->
            <label wire:key="{{ $i }}" style="background-image: url({{ $image->temporaryUrl() }});" class="relative ml-1 mb-1 inline-flex h-24 w-24 items-center justify-center bg-cover bg-center bg-no-repeat not-sortable cursor-pointer">
                
                <div class="block invisible">
                    <div class="align-center mx-auto flex h-auto w-10 fill-blue-700">
                        <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e"><path d="M19.4 17.44l-2.1-6.76-2.45 1.5-1.5-3.7-4 6.45-1.15-1.71-4.08 4.22 15.18-.05zM7.47 9.53A1.44 1.44 0 1 1 6 8.05a1.46 1.46 0 0 1 1.47 1.48zM2.93 5.08h18.14v13.84H2.93zM1.5 20.4h21V3.6h-21z" fill-rule="evenodd"></path></svg>
                    </div>
                    <div class="text-sm text-blue-700">Add a photo</div>
                </div>

                <button class="absolute top-1 right-1">
                    <svg width="18" height="18" viewBox="0 0 18 18" class="svg-wrapper--8ky9e"><g fill="none" fill-rule="evenodd"><path fill-opacity="0" fill="red" d="M0 0h18v18H0z"></path><g transform="translate(1 1)"><circle fill="#D95E46" cx="8" cy="8" r="8"></circle><rect fill="#FFF" x="4" y="7" width="8" height="2" rx="0.5"></rect></g></g></svg>
                </button>

                <input wire:model="images.{{ $i }}" type="file" class="hidden" />
            </label>
            
        @else

            {{-- new/edit :- no image selected --}}

            <!-- Active one -->
            <label wire:key="{{ $i }}" class="ml-1 mb-1 inline-flex h-24 w-24 items-center justify-center border border-blue-500 cursor-pointer">
                <div class="block">
                    <div class="align-center mx-auto flex h-auto w-10 fill-blue-700">
                    <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e"><path d="M19.4 17.44l-2.1-6.76-2.45 1.5-1.5-3.7-4 6.45-1.15-1.71-4.08 4.22 15.18-.05zM7.47 9.53A1.44 1.44 0 1 1 6 8.05a1.46 1.46 0 0 1 1.47 1.48zM2.93 5.08h18.14v13.84H2.93zM1.5 20.4h21V3.6h-21z" fill-rule="evenodd"></path></svg>
                    </div>
                    <div class="text-sm text-blue-700">Add a photo</div>
                </div>
                <input wire:model="images.{{ $i }}" type="file" class="hidden"/>
            </label>
                    
            <!-- Inactive one -->
            <label for="image.2" class="ml-1 mb-1 inline-flex h-24 w-24 items-center justify-center border border-gray-300 cursor-pointer">
            <div class="block">
                <div class="align-center mx-auto flex h-auto w-10 fill-gray-300">
                <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e"><path d="M19.4 17.44l-2.1-6.76-2.45 1.5-1.5-3.7-4 6.45-1.15-1.71-4.08 4.22 15.18-.05zM7.47 9.53A1.44 1.44 0 1 1 6 8.05a1.46 1.46 0 0 1 1.47 1.48zM2.93 5.08h18.14v13.84H2.93zM1.5 20.4h21V3.6h-21z" fill-rule="evenodd"></path></svg>
                </div>
                <div class="text-sm text-gray-300">Add a photo</div>
            </div>
            <input type="file" class="hidden" id="image.2" />
            </label>


        @endif
        
    @endfor

    

    <!-- Inactive one -->
    <label for="image.2" class="ml-1 mb-1 inline-flex h-24 w-24 items-center justify-center border border-gray-300 cursor-pointer">
      <div class="block">
        <div class="align-center mx-auto flex h-auto w-10 fill-gray-300">
          <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e"><path d="M19.4 17.44l-2.1-6.76-2.45 1.5-1.5-3.7-4 6.45-1.15-1.71-4.08 4.22 15.18-.05zM7.47 9.53A1.44 1.44 0 1 1 6 8.05a1.46 1.46 0 0 1 1.47 1.48zM2.93 5.08h18.14v13.84H2.93zM1.5 20.4h21V3.6h-21z" fill-rule="evenodd"></path></svg>
        </div>
        <div class="text-sm text-gray-300">Add a photo</div>
      </div>
      <input type="file" class="hidden" id="image.2" />
    </label>

    
    
  </div>
</div>