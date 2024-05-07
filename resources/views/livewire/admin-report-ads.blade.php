<div>
    @if($reportsExists)

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
        
        <table class="dataTable table table-bordered table-striped display dt-responsive nowrap" style="width:100%">
            <tr>
                <th>
                    <span>ID</span>
                </th>
                <th>Post</th>
                <th>Email</th>
                <th>Reason</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

            @foreach ($reports as $report)
                <tr wire:key="{{ $report->id }}">
                    <td>{{ $report->id }}</td>
                    <td style="text-transform: capitalize;">
                        <a href="{{ \App\Helpers\UrlGen::post($report->post) }}" title="{{ data_get($report->post, 'title') }}">{{ $report->post->title }}</a>
                    </td>
                    <td class="truncate"><a href="mailto:{{ $report->email }}" target="_blank">{{ $report->email }}</a></td>
                    <td style="text-transform: capitalize;">{{ $report->reason }}</td>
                    <td style="text-transform: capitalize;">{{ $report->message }}</td>
                    <td>{{ date('Y-m-d', strtotime($report->created_at)) }}</td>
                    <td>
                        <div>
                            <p>
                                <a title="Put Offline" class="btn btn-warning btn-sm confirm-simple-action" href="{{ route('posts.offline', ['pagePath' => 'list', 'id' => $report->post->id ]) }}">
                                    <i class="fas fa-eye-slash"></i> Put offline
                                </a>
                            </p>
                            <p>
                                <a wire:click="deleteReport({{ $report->id }})" title="Delete Ad" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Delete report
                                </a>
                            </p>
                        </div>
                    </td>
                </tr>
            @endforeach

        </table>

        <div class="flex justify-between align-middle">
            <div style="padding-top: 0.85em;">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries.</div>
            <div>{{ $reports->links() }}</div>
        </div>

    @else
        <div>No ads reports on posted ads.</div>
    @endif
</div>