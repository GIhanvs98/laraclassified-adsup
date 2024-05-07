<div>
    @if($districtsExists)

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
                <th>District</th>
                <th>Province</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach($districts as $key => $district)

            <tr wire:key="{{ $district->id }}">
                <td>{{ $district->id }}</td>
                <td>
                    {{ str_replace(' District', '', $district->name) }}
                    <a class="btn btn-xs btn-light" href="{{ route('admin.locations.cities.reorder', ['districtId' => $district->id ]) }}">Reorder Cities</a>
                </td>
                <td>
                    <select class="form-select cursor-pointer change-province" district-id="{{ $district->id }}">
                        @forelse($provinces as $key => $province)
                            @if(isset($district->subAdmin1))
                                @if( $province->id == $district->subAdmin1->id )
                                    <option selected value="{{ $province->id }}">{{ $province->name }}</option>
                                @else
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endif
                            @else
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endif
                        @empty
                            <option value="">No provinces</option>
                        @endforelse
                    </select>
                </td>
                <td>
                    <label class="relative mb-4 inline-flex cursor-pointer items-center">
                        <input wire:click="Status('{{ $district->id }}')" type="checkbox" @if($district->active) checked @endif class="peer sr-only" />
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-blue-300"></div>
                    </label>
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="flex justify-between align-middle">
        <div style="padding-top: 0.85em;">Showing {{ $districts->firstItem() }} to {{ $districts->lastItem() }} of {{ $districts->total() }} entries.</div>
        <div>{{ $districts->links() }}</div>
    </div>

    @else
        <div>No districts.</div>
    @endif
<script>
    document.addEventListener('livewire:initialized', () => {
        // Runs immediately after Livewire has finished initializing
        
        $(document).ready(function() {

            $(".change-province").on("click", function() {

                let provinceId = $(this).val();
                let districtId = $(this).attr("district-id");

                console.log(provinceId);

                @this.dispatch('change-province', { province: provinceId, district: districtId });

            });
        });
    });
</script>

</div>
