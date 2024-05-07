<div>

    @if($adsExists)
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
                        <th>Ad</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $key => $ad)

                        @php

                            $transaction = $ad->transactions()->successfull('ad-promotion')->latest('created_at')->first();

                            $transactionValid = $ad->transactions()->valid('ad-promotion')->latest('created_at')->first();

                        @endphp

                        @if($transaction)
                            <tr wire:key="{{ $ad->id }}">
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td style="text-transform: capitalize;">
                                    <a href="{{ \App\Helpers\UrlGen::post($ad) }}" class="block text-blue-500 mb-1" title="{{ data_get($ad, 'title') }}">{{ $ad->title }}</a>
                                    <div class="text-xs text-gray-600">
                                        {{ date_format(date_create($ad->created_at),"Y-m-d") }}&nbsp;at&nbsp;{{ date_format(date_create($ad->created_at),"H:i:s") }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 mb-1">
                                        {{ $ad->contact_name }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $ad->email }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        Type: @if(isset($ad->user_id)) User @else Guest @endif
                                    </div>
                                </td>
                                <td>
                                    @if($transactionValid)
                                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-green-600 w-fit">
                                            Valid
                                        </div>
                                    @else
                                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-red-600 w-fit">
                                            Expired
                                        </div>
                                    @endif
                                </td>
                                <td>
                                <div>
                                    <label for="datetime" class="text-sm text-gray-400 mb-1">Payment valid (date and time):</label>
                                    <input type="datetime-local" wire:model.change="datetime.{{ $ad->id }}.value" @disabled($datetime[$ad->id]['disabled'] ?? true) id="datetime" class="form-control text-sm border border-gray-300"> 
                                </div>
                                </td>
                                <td>
                                    @if($datetime[$ad->id]['disabled'])
                                        <button wire:click="edit({{ $ad->id }})" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1">
                                            Edit
                                        </button>
                                    @else
                                        <button wire:click="save({{ $ad->id }})" class="btn btn-xs btn-success flex items-center min-w-fit max-w-fit mb-1">
                                            Save
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between align-middle">
            <div style="padding-top: 0.85em;">Showing {{ $ads->firstItem() }} to {{ $ads->lastItem() }} of {{ $ads->total() }} entries.</div>
            <div>{{ $ads->links() }}</div>
        </div>

    @else
        <div>No promoted ads to edit the listing durations.</div>
    @endif

</div>