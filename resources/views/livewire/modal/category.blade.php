<div class="relative w-full max-w-4xl max-h-full">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow">
        <!-- Modal header -->
        <div class="flex items-start justify-between p-4 rounded-t pb-2">
            <h3 class="text-xl font-semibold text-gray-900 pb-0">
                Change Category
            </h3>
            <button id="changeCategoryButton" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="changeCategory">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <!-- Modal body -->
        <div class="p-6 space-y-6 pt-0">
            <div class="row mb-2" x-data="{ open: false }">

                @if(isset($categories))

                    {{-- Main Categories --}}
                    <div class="col">
                        <ul class="nav flex-column" style="border-bottom: 1px solid #d4ded9;">

                            <li style="padding: 0px;">
                                <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px !important;color: #2f3432;margin-bottom: 8px;font-size: 1rem !important;line-height: 1.33333333 !important;">Categories</h5>
                            </li>

                            <label class="nav-item locations-arrow cursor-pointer" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center; padding: 0.5rem 1rem 0.5rem 0px; color: #616664; margin-bottom: 0px; color: #0d6efd;">
                                <input type="radio" value="" class="category" name="category" hidden>All Categories
                            </label>

                            @foreach ($categories as $key => $category)

                                <label for="category_model_{{ $category->id }}" x-on:click="open = true" wire:key="{{ $key }}" wire:click="showSubCategories({{ $category->id }})" class="nav-item locations-arrow cursor-pointer" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;margin-bottom: 0px;align-items: center;">
                                    <input type="radio" id="category_model_{{ $category->id }}" value="{{ $category->id }}" class="category-2" name="category" hidden>
                                    <div type="button" class="nav-link button-list flex" style="padding-left: 0px;">
                                        <img src="{{ asset('storage/'.$category->picture) }}" class="col lazyload img-fluid" style="height: 20px;margin-right: 8px;" alt="{{ $category->name }} icon">
                                        {{ $category->name }}
                                    </div>
                                </label>

                            @endforeach

                        </ul>
                    </div>

                    {{-- Sub Categories --}}
                    <div class="col" {{--x-show="open" x-transition--}}>

                        <ul wire:loading.flex class="nav flex-column">
                            <li class="nav-item" style="display: flex;justify-content: space-between;align-items: center;">
                                <button class="nav-link button-list active">
                                    <div class="row row-featured row-featured-category">
                                        <div class="col-12 text-center">
                                            <svg aria-hidden="true" role="status" class="mr-3 inline h-4 w-4 animate-spin text-gray-200 dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2" />
                                            </svg>
                                            Loading...
                                        </div>
                                    </div>
                                </button>
                            </li>
                        </ul>


                            @if(isset($subCategories) && $subCategories->count() > 0)

                                <ul class="nav flex-column" wire:loading.remove style="border-bottom: 1px solid #d4ded9;">

                                    <li style="padding: 0px;">
                                        <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px !important;color: #2f3432;margin-bottom: 8px;font-size: 1rem !important;line-height: 1.33333333 !important;">Select a sub category of {{ $selectedCategory->name }}</h5>
                                    </li>

                                    <label class="nav-item locations-arrow cursor-pointer" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center; margin-bottom: 0px; padding: 0.5rem 1rem 0.5rem 0px; color: #0d6efd;">
                                        <input type="radio" value="{{ $selectedCategory->id }}" class="category" name="category" hidden>All {{ $selectedCategory->name }}
                                    </label>

                                    @foreach($subCategories as $key => $subCategory)

                                        <label wire:key="{{ $key }}" class="nav-item locations-arrow cursor-pointer" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center; margin-bottom: 0px; padding: 0.5rem 1rem 0.5rem 0px; color: #0d6efd;">
                                            <input type="radio" value="{{ $subCategory->id }}" class="subCategory" name="subCategory" hidden>{{ $subCategory->name }}
                                        </label>

                                    @endforeach

                                <ul>
                            @else

                                <ul class="nav flex-column" wire:loading.remove>

                                    <li style="padding: 0px;" x-show="open" x-transition>
                                        <h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px !important;color: #2f3432;margin-bottom: 8px;font-size: 1rem !important;line-height: 1.33333333 !important;">Sub Categories</h5>
                                    </li>

                                    <li class="nav-item" x-show="open" x-transition style="border-top: 1px solid #d4ded9;border-bottom: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
                                        <button class="nav-link button-list">
                                            No Sub Categories.
                                        </button>
                                    </li>

                                <ul>
                            @endif

                    </div>

                @else
                    <div class="col">No Categories</div>
                @endif

            </div>
        </div>

    </div>
</div>