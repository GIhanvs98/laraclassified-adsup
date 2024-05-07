<div class="container bg-white" style="border-radius: 4px;">
    <div class="row" style="justify-content: center;">
        <div class="banner-container" style='background-image: url("{{ \Illuminate\Support\Facades\Storage::url($user->shop->wallpaper) }}");'>
            <!--img class="logo" src="{{ $this->userPhotoUrl() }}" alt="{{ $user->name }}" data-testid="" loading="lazy" /-->
        </div>

        <div class="row">
            <div class="col-12 col-sm-12 col-lg-4 block">
                <div class="info-container md:pl-0 md:pr-0">
                    <div class="border border-gray-200 px-4 py-6" style="border-radius: 4px 4px 0px 0px;">
                        <h3 class="mb-1 text-xl font-semibold" style="padding-bottom: 0px !important;">{{ $user->name }}</h3>
                        <p class="text-gray-500" style="margin-bottom: 0px !important;">{{ $user->shop->title }}</p>

                        <div class="member-badges mt-2">
                            <div class="flex w-fit items-center justify-start rounded-xl bg-gray-300 p-1">
                                @if(isset($user->membership->icon) && $user->membership->icon != "")
                                <div class="membership-icon h-fit w-5">
                                    {!! $user->membership->icon !!}
                                </div>
                                @endif
                                <div class="mt-[0.1rem] h-fit px-1 pl-2 text-xs font-semibold text-gray-900" style="margin: 0px;">{{ strtoupper($user->membership->name) }}</div>
                            </div>
                        </div>

                        <div class="mt-2 text-sm">User since {{ date_format(date_create($user->membership->created_at), "F Y") }}</div>
                    </div>

                    <div class="border border-t-0 border-gray-200 px-4 py-6">
                        <div class="shop-timing-section--2oh9-">

                            @php
                            $currentDate = date('H:i');
                            $currentDate = date('H:i', strtotime($currentDate));

                            $startDate = date('H:i', strtotime(json_decode($user->shop->open_hours, true)[strtolower(date('l'))]['from']));
                            $endDate = date('H:i', strtotime(json_decode($user->shop->open_hours, true)[strtolower(date('l'))]['to']));

                            if (($currentDate >= $startDate) && ($currentDate <= $endDate)){ $openHourStatus='<span class="text-lg font-semibold text-green-700">Open</span>' ; $openHourSubStatus='<span class="text-gray-500">Closes ' .date('h:i a', strtotime( json_decode($user->shop->open_hours, true)[strtolower(date('l'))]['to'] )).'</span>';
                                }else{
                                $openHourStatus = '<span class="text-lg font-semibold" style="color: #d32318;">Closed</span>';
                                $openHourSubStatus = '<span class="text-gray-500">Open '.date('h:i a', strtotime( json_decode($user->shop->open_hours, true)[strtolower(date('l'))]['to'] )).'</span>';
                                }
                                @endphp

                                {!! $openHourStatus !!}
                                {!! $openHourSubStatus !!}
                                <div>
                                    <span data-modal-target="openHoursModal" data-modal-toggle="openHoursModal" class="text-sm text-blue-500" style="cursor: pointer"> See all timings </span>
                                </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-start border border-t-0 border-gray-200 px-4 py-6">
                        <livewire:contact-numbers :data-id="$user->id" type="shop" />

                        {{--
                            <div class="w-6 fill-blue-700">
                                <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
                                    <path d="M16 16.55a2.05 2.05 0 0 1 2.82-.74 15.12 15.12 0 0 1 2.08.85c.93.52 2.69 1.72 2.62 2.9A5.57 5.57 0 0 1 21.2 23c-2.45 1.76-5.41.79-8-.12A20.14 20.14 0 0 1 .75 10.37a11.38 11.38 0 0 1-.7-4.69A6.23 6.23 0 0 1 1.84 2C2.41 1.44 3.62.32 4.46.24 6.13-1 7.5 2.89 7.83 4c.27.93 1 2.8.18 3.56-.65.57-2.57 2.49-2.33 3.29.63 2.11 3.17 4.07 4.77 5.43a12.3 12.3 0 0 0 2.94 2c1.17.5 1.83-1.06 2.64-1.75.17-.18-.17.16-.03.02z" fill-rule="evenodd"></path>
                                </svg>
                            </div>

                            <div class="ml-4">
                                <div>@php if(isset($user->phone) & $user->phone != ""){echo $user->phone;}else{echo "-";} @endphp</div>
                                <div class="text-sm text-gray-500">Phone number</div>
                            </div>
                        --}}
                    </div>

                    <div class="flex items-center justify-start border border-t-0 border-gray-200 px-4 py-6">
                        <div class="w-6 fill-green-700">
                            <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
                                <path d="M24 3.79l-9 10.05a4.22 4.22 0 0 1-3 1.64 4.36 4.36 0 0 1-3-1.7L0 3.79v13.68a8.94 8.94 0 0 0 .1 1.27l5.23-5.37 1.34 1.35-5.34 5.37a10.11 10.11 0 0 0 1.34.12H22a1.69 1.69 0 0 0 .53-.12l-5.2-5.37 1.34-1.35 5.15 5.37a5.52 5.52 0 0 0 .18-1.52V3.79zm-11.24 9.27l8.57-9.27H2.67l8.57 9.27a1.26 1.26 0 0 0 .76.44 1.26 1.26 0 0 0 .76-.44z" fill-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <a href="mailto:{{ $user->email }}" target="_blank">
                                <div>Send Message</div>
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start justify-start border border-t-0 border-gray-200 px-4 py-6">
                        <div class="w-8 min-w-[24px] max-w-[25px] fill-gray-500">
                            <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                                <path d="M30 10c-8.4 0-15.3 6.7-15.3 15 0 4.7 2.3 10.2 6.8 16.5 3.3 4.5 6.5 7.7 6.6 7.8.5.5 1.1.7 1.8.7s1.3-.2 1.8-.7c.1-.1 3.4-3.3 6.6-7.8 4.5-6.2 6.8-11.8 6.8-16.5.2-8.3-6.7-15-15.1-15zm0 8.8c3.5 0 6.4 2.8 6.4 6.2s-2.9 6.2-6.4 6.2c-3.5 0-6.4-2.8-6.4-6.2s2.9-6.2 6.4-6.2"></path>
                            </svg>
                        </div>
                        <div class="ml-4 text-sm">
                            <div>{{ $user->shop->address }}</div>
                        </div>
                    </div>

                    <div class="border border-t-0 border-gray-200 px-4 py-6" style="border-radius: 0px 0px 4px 4px;">
                        <div>
                            <div>About the shop</div>
                            <div class="mt-2 {{ $showMore ? '' : 'line-clamp-4' }} text-sm text-gray-600">{{ $user->shop->description }}</div>
                        </div>
                        @if(!$showMore)
                            <div class="mt-2 flex items-center justify-start fill-gray-500 text-sm text-gray-600" wire:click="$toggle('showMore')" style="display: inline-flex;cursor: pointer;">
                                <div>Show more</div>
                                <span class="ml-2 inline-block w-4">
                                    <svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
                                        <path d="M4.35 5.47L12 13.54l7.65-8.07L22 7.96 12 18.53 2 7.96l2.35-2.49z"></path>
                                    </svg>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-12 col-lg-8 block">
                <div class="serp-container">

                    <div class="category-list make-list">
                        <div class="listing-filter">
                            <div class="container bg-white" style="padding: 0px">
                                <nav aria-label="breadcrumb" role="navigation" class="search-breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ url('/') }}"><i class="fas fa-home"></i></a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ url('/search') }}"> Sri Lanka </a>
                                        </li>
                                        <li class="breadcrumb-item active">Shops &nbsp;</li>
                                    </ol>
                                </nav>
                            </div>

                            <div class="col-xl-6 col-md-6 col-sm-12 col-12 px-1 py-sm-1 bg-primary rounded search-field-btn" style="border-radius: 50px !important;background: transparent !important;border: 1px solid rgb(112, 118, 118) !important;margin: 10px 0px 20px 0px;width: -webkit-fill-available;">
                                <div class="row gx-1 gy-1">

                                    <input class="form-control locinput input-rel searchtag-input" type="text" id="catSearch" name="c" hidden="" value="">

                                    <div class="col-10" style="align-items: center;display: flex;">
                                        <input wire:model.live="query" wire:keydown.enter="$refresh" class="form-control keyword" type="text" placeholder="What are you looking for?" value="" style="border: 0px;margin-left: 10px;outline: none;box-shadow: none;" autocomplete="off">
                                    </div>

                                    <div class="col-2" style="justify-content: end;display: flex;">
                                        <button wire:click="$refresh" class="btn btn-block btn-primary search-field-btn" style="outline: 0px;background: rgb(255, 200, 0) !important;border-radius: 100px !important;display: flex;color: #424e4e;width: 45px;justify-content: center;align-items: center;margin: 0px;position: relative;height: 45px;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <div class="float-start col-md-9 col-sm-8 col-12">
                                <h1 class="h6 breadcrumb-list pb-0">
                                    <a href="http://127.0.0.1:8000/search" class="current"><span>All ads from <span style="text-transform: capitalize;">{{ $user->shop->title }}</span> ({{ $posts->firstItem() }}-{{ $posts->lastItem() }} of {{ $posts->total() }})</span></a>
                                </h1>
                                <div style="clear:both;"></div>
                            </div>

                            <div style="clear:both"></div>
                        </div>


                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="contentAll" role="tabpanel" aria-labelledby="tabAll">
                                <div id="postsList" class="category-list-wrapper posts-wrapper row no-margin">
                                    <div class="col-12" style="padding-left: 0px;">

                                        @each('shops.listing-item', $posts, 'post', 'shops.empty')

                                    </div>
                                </div>
                                <div class="flex justify-between align-middle text-gray-500">
                                    <div style="padding: 15px;padding-top: 0.85em;">Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} entries.</div>
                                    <div>{{ $posts->links() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Open Hours modal -->
    <div id="openHoursModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" style="padding: 0px;">
                        Open Hours
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="openHoursModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6" style="padding-top: 0px;">

                    @if(isset($user->shop->open_hours))

                    @foreach( array_merge(array_flip($order), json_decode($user->shop->open_hours, true)) as $dayname => $time)
                    <div class="row w-full">
                        <div class="col-4" style="text-transform: capitalize;">{{ $dayname }}</div>
                        <div class="col-3" style="text-align: right;">@php if(isset($time['from']) & $time['from'] != ""){echo date('h:i a', strtotime($time['from']));}else{echo "-";} @endphp</div>
                        <div class="col-2" style="text-align: center;">-</div>
                        <div class="col-3" style="text-align: left;">@php if(isset($time['to']) & $time['to'] != ""){echo date('h:i a', strtotime($time['to']));}else{echo "-";} @endphp</div>
                    </div>
                    @endforeach
                    @else
                    <center>No time slots given</center>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
