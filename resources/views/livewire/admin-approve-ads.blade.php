<div>

    @if($ads)

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $key => $ad)
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
                                @if($ad->reviewingViolation)
                                    @if($ad->reviewingViolation->rechecked_datetime)
                                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-green-600 w-fit">
                                            Rechecked
                                        </div>
                                    @else
                                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-red-600 w-fit">
                                            Unchecked
                                        </div>
                                    @endif
                                @else
                                    <div class="inline text-xs px-2 py-1 rounded-full text-white bg-green-600 w-fit">
                                        No issues
                                    </div>
                                @endif
                            </td>
                            <td>
                                <button wire:click="approve({{ $ad->id }})" class="btn btn-xs btn-success flex items-center min-w-fit max-w-fit mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Authorize Ad</span>
                                </button>

                                @if($ad->reviewingViolation)
                                    <button wire:click="$dispatch('show-view-revision', { type: 'edit', post: {{ $ad->id }} })" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>     
                                        <span>View Revision</span>
                                    </button>
                                @else
                                    <button wire:click="$dispatch('show-view-revision', { type: 'create', post: {{ $ad->id }} })" class="btn btn-xs btn-secondary flex items-center min-w-fit max-w-fit mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <span>Add Revision</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between align-middle">
            <div style="padding-top: 0.85em;">Showing {{ $ads->firstItem() }} to {{ $ads->lastItem() }} of {{ $ads->total() }} entries.</div>
            <div>{{ $ads->links() }}</div>
        </div>

    @else
        <div>No ad reviews left.</div>
    @endif

    {{-- CRUD Revision modal --}}
    <div id="revisionModal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-[60] hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0" wire:ignore.self>
        @if($selectedAd)
            <div class="relative max-h-full w-full max-w-2xl">
            <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow w-full">
                    <!-- Modal header -->
                    <div class="items-start justify-between rounded-t border-b p-5 flex justify-between">
                        <h3 class="block text-xl font-semibold text-gray-900 lg:text-2xl">
                            Revision for `{{ $selectedAd->title }}`
                        </h3>
                        <button wire:click="$dispatch('hide-view-revision')" type="button" class="block h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900">
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-6 p-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 ">Reason</label>
                            <textarea id="reason" wire:model.change="reason" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border @if($errors->has('reason')) border-red-600 @else border-gray-300 @endif" placeholder="Write a description..."></textarea>         
                            <div class="text-red-600 text-xs mt-1">@error('reason') {{ $message }} @enderror</div>           
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center space-x-2 rtl:space-x-reverse pt-0 rounded-b p-5">
                        
                        @if($ad->reviewingViolation)
                            <button wire:click="edit" class="btn btn-md btn-secondary flex items-center min-w-fit max-w-fit mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span>Update Revision</span>
                            </button>

                            <button wire:click="delete" class="btn btn-md btn-danger flex items-center min-w-fit max-w-fit mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>Delete Revision</span>
                            </button>
                        @else
                            <button wire:click="create" class="btn btn-md btn-secondary flex items-center min-w-fit max-w-fit mb-1">
                                Submit
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        @else
            <div class="relative max-h-full w-fit max-w-2xl">
                <!-- Modal content -->
                <div class="relative rounded-full text-center w-fit p-4">
                    <div role="status">
                        <svg aria-hidden="true" class="inline w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
        <script type="module">

            document.addEventListener('livewire:initialized', () => {

                // set the view revisions modal
                const targetEl = document.getElementById('revisionModal');

                // options with default values
                const options = {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses:
                        'bg-gray-900/50 fixed inset-0 z-50',
                    closable: true
                };

                const modal = new Modal(targetEl, options);

                $wire.on('show-view-revision', () => {
                    modal.show();
                });

                $wire.on('hide-view-revision', () => {
                    modal.hide();
                });

            });

        </script>
    @endscript

</div>