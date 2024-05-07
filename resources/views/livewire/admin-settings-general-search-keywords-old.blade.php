<div>
    @if($searchKeywords)

        <div class="table-action">
            <div class="table-search">
                <div class="row justify-content-between">
                    <div class="col-md col-12">
                        <a href="#" class="btn btn-primary shadow ladda-button" wire:click="$dispatch('show-modal', { type: 'new' })">
                            <span class="ladda-label"><i class="fas fa-plus"></i> Add New</span>
                        </a>
                    </div>
                    <div class="col-md col-12 row">
                        <label style="max-width: fit-content;" class="col form-label text-end">{{ t('search') }} <br>
                            <a title="clear filter" class="clear-filter" wire:click="searchClear">[{{ t('clear') }}]</a>
                        </label>
                        <div class="col-md col-12 searchpan px-3">
                            <input type="text" class="form-control text-sm border border-gray-300" wire:model.live="search">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><span>ID</span></th>
                    <th>Category</th>
                    <th>Keywords</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach($searchKeywords as $key => $searchKeyword)

                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $searchKeyword->category->name }}</td>
                        <td><div class="line-clamp-4">{{ $searchKeyword->keywords }}</div></td>
                        <td>
                            <button wire:click="$dispatch('show-modal', { type: 'edit', keywordGroupId: '{{ $searchKeyword->id }}' })" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1">
                                Edit
                            </button>
            
                            <button wire:click="remove('{{ $searchKeyword->id }}')" class="btn btn-xs btn-danger flex items-center min-w-fit max-w-fit mb-1">
                                Remove
                            </button>
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>

        <div class="flex justify-between align-middle">
            <div style="padding-top: 0.85em;">Showing {{ $searchKeywords->firstItem() }} to {{ $searchKeywords->lastItem() }} of {{ $searchKeywords->total() }} entries.</div>
            <div>{{ $searchKeywords->links() }}</div>
        </div>

    @else
        <div>No search keywords yet.</div>
    @endif


    {{-- CRUD Add Search Keyword modal --}}
    <div id="keywordsModal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-[60] hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0" wire:ignore.self>
        @if($modal['state'] == "new" || ($modal['state'] == "edit" && $modal['selected']))
            <div class="relative max-h-full w-full max-w-2xl">
            <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow w-full">
                    <!-- Modal header -->
                    <div class="items-start justify-between rounded-t border-b p-5 flex justify-between">
                        <h3 class="block text-xl font-semibold text-gray-900 lg:text-2xl">
                            @if($modal['state'] == "new")
                                New Keyword Group
                            @elseif($modal['state'] == "edit")
                                Edit Keywords of `{{ $modal['selected']->category->name }}` group
                            @endif
                        </h3>
                        <button wire:click="$dispatch('hide-modal')" type="button" class="block h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900">
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-6 p-5 grid gap-4 sm:grid-cols-2">
                        @if($modal['state'] == "new")
                            <div class="sm:col-span-2">
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 ">Category</label>
                                <select wire:model.change="category.value" id="category" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border @if($errors->has('reason')) border-red-600 @else border-gray-300 @endif">
                                    <option value="" selected disabled>Select a category</option>
                                    @foreach($category['options'] as $key => $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-red-600 text-xs mt-1">@error('category.value') {{ $message }} @enderror</div>           
                            </div>
                        @endif

                        <div class="sm:col-span-2" style="margin: 0px;">
                            <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 ">Keywords</label>
                            <textarea id="keywords" wire:model.change="keywords" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border @if($errors->has('reason')) border-red-600 @else border-gray-300 @endif" placeholder="Enter your keywords. Eg:- Vehicles, Toys, Furnitures..."></textarea>         
                            <div class="text-red-600 text-xs mt-1">@error('keywords') {{ $message }} @enderror</div>           
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center space-x-2 rtl:space-x-reverse pt-0 rounded-b p-5">
                        <!-- Start condition -->
                        <button wire:click="submit" class="btn btn-md btn-secondary flex items-center min-w-fit max-w-fit mb-1">
                            Submit
                        </button>
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
                const targetEl = document.getElementById('keywordsModal');

                // options with default values
                const options = {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses:
                        'bg-gray-900/50 fixed inset-0 z-50',
                    closable: true
                };

                const modal = new Modal(targetEl, options);

                $wire.on('show-modal', () => {
                    modal.show();
                });

                $wire.on('hide-modal', () => {
                    modal.hide();
                    $wire.$set('modal.state', null);
                });

            });

        </script>
    @endscript

    <style>
        .tag.label{
            background-color: #17a2b8;
            padding: 2px 10px;
            border-radius: 6px; 
        }

        .line-clamp-4{
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 4;
        }
    </style>
</div>
