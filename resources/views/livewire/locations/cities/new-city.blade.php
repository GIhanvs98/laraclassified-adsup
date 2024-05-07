<form wire:submit="save">

    <div class="card-body">

        <div id="loadingData"></div>

        <div class="card mb-0">
            <div class="row">

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">City Name</label>
                    <input wire:model.blur="name" type="text" placeholder="Name" class="form-control">
                    @error('name') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" wire:model.blur="status" value="1" name="status" class="form-check-input" style="cursor: pointer;">
                        <label class="form-check-label fw-bolder">
                            Status
                        </label>
                        @error('status') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">Longitude</label>
                    <input type="text" wire:model.blur="longitude" placeholder="Longitude of the city" class="form-control">
                    @error('longitude') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">Latitude</label>
                    <input type="text" wire:model.blur="latitude" placeholder="Latitude of the city" class="form-control">
                    @error('latitude') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">Population</label>
                    <input type="number" wire:model.blur="population" placeholder="Population of the city" class="form-control">
                    @error('population') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">Order</label>
                    <input type="number" wire:model.blur="order" placeholder="Position of the city" class="form-control">
                    @error('order') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">City Type</label>
                    <div style="display: flex;">
                        <select class="form-select" wire:model.blur="feature_code">
                            <option selected disabled value="">Select the city</option>
                            <option value="PPL">Normal City</option>
                            <option value="PPLA">Major City</option>
                            <option value="PPLA3">Export City</option>
                            <option value="PPLA">Capital City</option>
                        </select>
                    </div>
                    @error('feature_code') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label fw-bolder">District</label>
                    <div style="display: flex;">
                        <select class="form-select" wire:model.blur="districtId">
                            <option selected disabled value="">Select the district</option>
                            @forelse($districts as $key => $district)
                                <option value="{{ $district->id }}">{{ str_replace(' District', '', $district->name) }}</option>
                            @empty
                                <option value="">No districts</option>
                            @endforelse
                        </select>
                    </div>
                    @error('districtId') <span class="error" style="margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

            </div>
        </div>

        @empty(!$error_output)
            <div style="margin-top: 20px;" class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>

                <span class="sr-only">Info</span>

                <div>{!! $error_output !!}</div>
            </div>
        @endempty

    </div>


    <div class="card-footer" style="padding-left: 0px;">
        <div>
            <button type="submit" class="btn btn-primary shadow">

                <i class="fa fa-save" wire:loading.remove></i>

                <svg wire:loading aria-hidden="true" role="status" class="inline w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                </svg>
                &nbsp;
                <span>Save and back</span>
            </button>

            <button type="reset" class="btn btn-secondary shadow"><span class="fa fa-ban"></span> &nbsp;Reset</button>
        </div>
    </div>

</form>