<div>
    @if($citiesExists)

    <div class="table-action">
        <div class="table-search">
            <div class="row">
                <label style="max-width: fit-content;" class="col form-label text-end">{{ t('search') }} <br>
                    <a title="clear filter" class="clear-filter" wire:click="searchClear">[{{ t('clear') }}]</a>
                </label>
                <div class="col-md-4 col-12 searchpan px-3">
                    <input type="text" class="form-control" wire:model.live="search">
                </div>
            </div>
        </div>
    </div>
    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th><span>ID</span></th>
                <th>city</th>
                <th>district</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

            @foreach($cities as $key => $city)

            <tr wire:key="{{ $city->id }}">
                <td>{{ $city->id }}</td>
                <td>
                    {{ $city->name }}
                </td>
                <td>
                    <select class="form-select cursor-pointer change-district" city-id="{{ $city->id }}">
                        @forelse($districts as $key => $district)
                        @if(isset($city->subAdmin2))
                        @if( $district->id == $city->subAdmin2->id )
                        <option selected value="{{ $district->id }}">{{ str_replace(' District', '', $district->name) }}</option>
                        @else
                        <option value="{{ $district->id }}">{{ str_replace(' District', '', $district->name) }}</option>
                        @endif
                        @else
                        <option value="{{ $district->id }}">{{ str_replace(' District', '', $district->name) }}</option>
                        @endif
                        @empty
                        <option value="">No districts</option>
                        @endforelse
                    </select>
                </td>
                <td>
                    <label class="relative mb-4 inline-flex cursor-pointer items-center">
                        <input wire:click="Status('{{ $city->id }}')" type="checkbox" @if($city->active) checked @endif class="peer sr-only" />
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-blue-300"></div>
                    </label>
                </td>
                <td style="width: 16%;">

                    <a href="{{ route('admin.locations.cities.edit', ['cityId' => $city->id]) }}" style="margin-right: 5px;">
                        <button style="margin-right: 5px; display:inline-block;" class="btn btn-xs btn-primary">
                            <i class="bi bi-pen"></i> Edit
                        </button>
                    </a>

                    <button style="display:inline-block;" class="btn btn-xs btn-danger delete-btn" delete-btn-group="delete-btn-group-{{ $city->id }}">
                        <i class="fas fa-times"></i> Delete
                    </button>

                    <div class="btn-group delete-btn-group" role="group" style="display: none;width: 57px;" id="delete-btn-group-{{ $city->id }}">
                        <button type="button" class="btn btn-success btn-sm" wire:click="deleteCity({{ $city->id }})" title="Confirm"><i class="bi bi-check"></i></button>
                        <button type="button" class="btn btn-danger btn-sm btn-danger-2 delete-cancel" title="Cancel" flex><i class="bi bi-x"></i></button>
                    </div>

                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="flex justify-between align-middle">
        <div style="padding-top: 0.85em;">Showing {{ $cities->firstItem() }} to {{ $cities->lastItem() }} of {{ $cities->total() }} entries.</div>
        <div>{{ $cities->links() }}</div>
    </div>

    @else
    <div>No cities.</div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Runs immediately after Livewire has finished initializing

            $(document).ready(function() {

                $(document).on("click",".delete-btn",function() {

                    element = this;

                    $(element).hide();

                    let buttonGroup = $(element).attr("delete-btn-group");

                    $("#"+buttonGroup).show();

                    $(document).on("click",".delete-cancel",function() {

                        $(this).parent().hide();

                        $(element).show();

                    });

                });

                $(".change-district").on("click", function() {

                    let districtId = $(this).val();
                    let cityId = $(this).attr("city-id");

                    console.log(districtId);

                    @this.dispatch('change-district', { district: districtId, city: cityId});

                });
            });
        });

    </script>

</div>