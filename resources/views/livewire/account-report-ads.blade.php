<div>

    @if(isset($reports))
    
        {{--<div class="table-action">
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
        </div>--}}
    
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>
                            <span>ID</span>
                        </th>
                        <th>Post</th>
                        <th>Email</th>
                        <th>Reason</th>
                        <th>Message</th>
                        <th>{{ t('Date') }}</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach( $reports as $report )
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
                </tbody>
            </table>
        </div>

        <div>
            {{ $reports->links() }}
        </div>

    @else
        <div class="text-center mt10 mb30">You don't have any reports on posted ads.</div>
    @endif
</div>
