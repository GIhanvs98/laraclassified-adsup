<div>

    @if($usersExists)
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
                        <th>User</th>
                        <th>Membership</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)

                        @php

                            $transaction = $user->transactions()->successfull('membership')->latest('created_at')->first();

                            $transactionValid = $user->transactions()->valid('membership')->latest('created_at')->first();

                        @endphp

                        @if($transaction)
                            <tr wire:key="{{ $user->id }}">
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 mb-1">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td style="text-transform: capitalize;">
                                    <span class="w-3 h-3 d-inline-block">{!! $user->membership->icon !!}</span>
                                    {{ $user->membership->name }}
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
                                    <input type="datetime-local" wire:model.change="datetime.{{ $user->id }}.value" @disabled($datetime[$user->id]['disabled'] ?? true) id="datetime" class="form-control text-sm border border-gray-300"> 
                                </div>
                                </td>
                                <td>
                                    @if($datetime[$user->id]['disabled'])
                                        <button wire:click="edit({{ $user->id }})" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1">
                                            Edit
                                        </button>
                                    @else
                                        <button wire:click="save({{ $user->id }})" class="btn btn-xs btn-success flex items-center min-w-fit max-w-fit mb-1">
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
            <div style="padding-top: 0.85em;">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries.</div>
            <div>{{ $users->links() }}</div>
        </div>

    @else
        <div>No memberships to edit the membership durations.</div>
    @endif

</div>