<div>
    @if($transactionsExists)

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
                <th>User</th>
                <th>{{ t('Description') }}</th>
                <th>{{ t('Payment Method') }}</th>
                <th>{{ t('Value') }}</th>
                <th>{{ t('Date') }}</th>
                <th>{{ t('Status') }}</th>
                <th>Valid</th>
            </tr>
        </thead>
        <tbody>

            @foreach($transactions as $key => $transaction)

            <tr>
                <td>{{ $key+1 }}</td>
                <td>
                    <div class="text-sm text-gray-900 mb-1">
                        {{ $transaction->name }}
                    </div>
                    <div class="text-xs text-gray-600" style="font-size: .75rem; line-height: 1rem; color: rgb(75 85 99);">
                        {{ $transaction->email }}
                    </div>
                </td>
                <td>
                    @if ($transaction->payment_type == "membership")
                    <strong>`{{ ucfirst($transaction->membership->name) }}`</strong> membership<br>
                    @endif

                    @if ($transaction->payment_type == "ad-promotion")
                    <a href="{{ \App\Helpers\UrlGen::post($transaction->post) }}">{{ $transaction->post->title ?? 'Unknown' }}</a><br>
                    @endif

                    <div style="font-size: 10px; margin-top: 4px;">
                        {{ t('type') }} - {{ ucfirst($transaction->payment_type) }} payment<br>
                        @isset($transaction->payment_valid_untill_datetime)
                        Valid untill - {{ $transaction->payment_valid_untill_datetime }} {{ t('days') }}
                        @endisset
                    </div>
                </td>
                <td>{{ ucfirst($transaction->payment_method) }}</td>
                <td>{!! $transaction->currency->symbol .".". $transaction->net_amount !!}</td>
                <td>
                    {{ date_format(date_create($transaction->payment_started_datetime),"Y-m-d") }}
                    &nbsp;at&nbsp;
                    {{ date_format(date_create($transaction->payment_started_datetime),"H:i:s") }}
                </td>
                <td>
                    @if ($transaction->payment_status == "success")
                    <span class="badge bg-success">{{ ucfirst($transaction->payment_status) }}</span>
                    @else
                    <span class="badge bg-info">{{ ucfirst($transaction->payment_status) }}</span>
                    @endif
                </td>
                <td>
                    @if ($transaction->active == 1)
                    <span class="badge bg-success">Valid</span>
                    @else
                    <span class="badge bg-info">{{ t('Expired') }}</span>
                    @endif
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="flex justify-between align-middle">
        <div style="padding-top: 0.85em;">Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries.</div>
        <div>{{ $transactions->links() }}</div>
    </div>

    @else
    <div>No transactions done yet.</div>
    @endif
</div>
