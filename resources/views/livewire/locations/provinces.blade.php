<div>
    @if($provincesExists)

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
                <th>Province</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach($provinces as $key => $province)

            <tr wire:key="{{ $province->id }}">
                <td>{{ $province->id }}</td>
                <td>{{ $province->name }}</td>
                <td>
                    <label class="relative mb-4 inline-flex cursor-pointer items-center">
                        <input wire:click="Status('{{ $province->id }}')" type="checkbox"  @if($province->active) checked @endif class="peer sr-only" />
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-blue-300"></div>
                    </label>
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="flex justify-between align-middle">
        <div style="padding-top: 0.85em;">Showing {{ $provinces->firstItem() }} to {{ $provinces->lastItem() }} of {{ $provinces->total() }} entries.</div>
        <div>{{ $provinces->links() }}</div>
    </div>

    @else
    <div>No provinces.</div>
    @endif
</div>