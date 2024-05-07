<div>

    @if($shopsExists)
        <div class="table-action mb-3">
            <div class="table-search">
                <div class="row">
                    <label style="max-width: fit-content;" class="col form-label text-end">{{ t('search') }} <br>
                        <a title="clear filter" class="clear-filter text-blue-500" wire:click="searchClear">[{{ t('clear') }}]</a>
                    </label>
                    <div class="col-md-4 col-12 searchpan px-3">
                        <input type="text" class="form-control text-sm border border-gray-300" wire:model.live="search">
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Shop</th>
                        <th>User</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shops as $key => $shop)
                        <tr wire:key="{{ $shop->id }}">
                            <td>
                                {{ $key+1 }}
                            </td>
                            <td>
                                <a href="{{ route('shops.index', ['id'=> $shop->id, 'slug'=> \Illuminate\Support\Str::slug($shop->title, '-')]) }}" class="block text-blue-500 mb-1" title="{{ $shop->title }}">
                                    {{ ucwords($shop->title) }}
                                </a>
                            </td>
                            <td style="text-transform: capitalize;">
                                <div class="text-sm text-gray-900 mb-1">
                                    {{ $shop->user->name }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $shop->user->email }}
                                </div>
                            </td>
                            <td>
                            <div>
                                <label for="title" class="text-sm text-gray-400 mb-1">Title:</label>
                                <input type="text" wire:model.change="title.{{ $shop->id }}.value" @disabled($title[$shop->id]['disabled'] ?? true) id="title" placeholder="Title" class="form-control text-sm border border-gray-300"> 
                            </div>
                            </td>
                            <td>
                                @if($title[$shop->id]['disabled'])
                                    <button wire:click="edit({{ $shop->id }})" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1">
                                        Edit
                                    </button>
                                @else
                                    <button wire:click="save({{ $shop->id }})" class="btn btn-xs btn-success flex items-center min-w-fit max-w-fit mb-1">
                                        Save
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between align-middle">
            <div style="padding-top: 0.85em;">Showing {{ $shops->firstItem() }} to {{ $shops->lastItem() }} of {{ $shops->total() }} entries.</div>
            <div>{{ $shops->links() }}</div>
        </div>

    @else
        <div>No shops to edit the shops details.</div>
    @endif

</div>