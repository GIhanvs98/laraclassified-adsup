{{--
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@php
$addListingUrl = (isset($addListingUrl)) ? $addListingUrl : \App\Helpers\UrlGen::addPost();
$addListingAttr = '';

if (!auth()->check()) {
if (config('settings.single.guests_can_post_listings') != '1') {
$addListingUrl = '#quickLogin';
$addListingAttr = ' data-bs-toggle="modal"';
}
}
@endphp

@section('content')

@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])

<div class="main-container inner-page">

    <div class="container" style="background: white;padding:0px;border-radius: 4px;">
        <div class="inner-box category-content bg-white" style="margin-bottom: 0px;padding: 0px;">

            <form action="{{ route('search') }}" method="GET" id="searchForm">

                <input type="text" name="p" id="price" value="{{ base64_encode(json_encode($price)) }}" readonly hidden>

                <input type="text" name="c" id="category" value="{{ base64_encode(json_encode($category)) }}" readonly hidden>

                <input type="text" name="l" id="location" value="{{ base64_encode(json_encode($location)) }}" readonly hidden>

                <div class="search-header w-full items-center border-b py-3 d-block d-sm-flex sm:px-3">
                    <div class="col-12 col-sm-3 sm-mx-3 mx-0 sm:mb-3">
                        <button data-modal-target="changeLocation" data-modal-toggle="changeLocation" type="button" class="mr-2 w-full rounded-lg bg-[--primary-color] px-3 py-3 text-sm font-medium text-white focus:ring-4 focus:ring-blue-300 flex justify-center items-center changeLocationButton">
                            <div style="width: 20px; height: 20px; fill: white; margin-right: 5px; margin-bottom: 2px; display: inline-block;pointer-events: none; vertical-align: middle;">
                                <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                                    <path d="M30 10c-8.4 0-15.3 6.7-15.3 15 0 4.7 2.3 10.2 6.8 16.5 3.3 4.5 6.5 7.7 6.6 7.8.5.5 1.1.7 1.8.7s1.3-.2 1.8-.7c.1-.1 3.4-3.3 6.6-7.8 4.5-6.2 6.8-11.8 6.8-16.5.2-8.3-6.7-15-15.1-15zm0 8.8c3.5 0 6.4 2.8 6.4 6.2s-2.9 6.2-6.4 6.2c-3.5 0-6.4-2.8-6.4-6.2s2.9-6.2 6.4-6.2"></path>
                                </svg>
                            </div>
                            <div class="locationText truncate">Location</div>
                        </button>
                    </div>

                    <div class="col-12 col-sm-3 sm-mx-3 mx-0 sm:mb-3">
                        <button data-modal-target="changeCategory" data-modal-toggle="changeCategory" type="button" class="mr-2 w-full rounded-lg bg-[--primary-color] px-3 py-3 text-sm font-medium text-white focus:ring-4 focus:ring-blue-300 flex justify-center items-center changeCategoryButton">
                            <div style="width: 20px;height: 20px;fill: white;margin-right: 5px;display: inline-block;pointer-events: none;vertical-align: middle;">
                                <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                                    <path d="M47.834 26.901l-2.56-9.803c-.448-1.874-1.41-2.85-3.256-3.307l-9.655-2.599c-1.846-.456-3.134-.124-4.478 1.24L12.007 28.555c-1.343 1.364-1.343 3.596 0 4.96L25.85 47.57a3.427 3.427 0 0 0 4.885 0l15.878-16.122c1.344-1.364 1.67-2.672 1.22-4.547zm-12.62-2.894a3.546 3.546 0 0 1 0-4.96 3.418 3.418 0 0 1 4.885 0 3.545 3.545 0 0 1 0 4.96 3.417 3.417 0 0 1-4.886 0z"></path>
                                </svg>
                            </div>
                            <div class="categoryText truncate">Category</div>
                        </button>
                    </div>

                    <div class="d-block d-sm-none col-12 sm-mx-3 mx-0 sm:mb-3" id="show-filters">
                        <button type="button" class="mr-2 w-full rounded-lg bg-[--primary-color] px-5 py-3 text-sm font-medium text-white focus:ring-4 focus:ring-blue-300">Change Filters</button>
                    </div>

                    <div class="col-12 col-sm sm-mx-3 mx-0">
                        <label for="search" class="sr-only mb-2 text-sm font-medium text-gray-900">Search</label>
                        <div class="relative w-full">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" name="q" id="search" placeholder="Search Adsup" value="{{ $query }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500" style="padding: 16px 16px 16px 40px !important;" />
                            <button type="submit" class="absolute bottom-2.5 right-2.5 rounded-lg bg-[--primary-color] px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-4 focus:ring-blue-300">Search</button>
                        </div>
                    </div>
                </div>

                <div class="search-body flex w-full">
                    <div class="slide-filter d-none d-sm-block col-12 col-sm-4 col-md-3 border-r mb-2" id="filters-section">
                        <div class="pt-3 pb-1 mx-3 text-xs">

                            <div class="flex items-center justify-between pt-1 pb-3 d-flex d-sm-none">
                                <div class="text-lg font-semibold text-gray-800">Filters</div>
                                <div class="w-5 h-5 text-gray-800" id="close-filters">
                                    <svg viewBox="0 0 60 60" class="svg-wrapper--8ky9e">
                                        <path d="M10 45.6L45.7 10l4.3 4-35.7 36z"></path>
                                        <path d="M10 14.4l4.3-4.3L50 46l-4.3 4z"></path>
                                    </svg>
                                </div>

                            </div>

                            <div class="input-group">
                                <div class="text-xs text-gray-500 flex">Sort results by</div>
                                <select class="border w-full mt-1 mb-1 p-2 cursor-pointer text-sm sort" style="font-size: 14px;" name="sort">
                                    <option value="date_new_top" @if(!isset($sort) || $sort=="date_new_top" ) selected @endif>Date: Newest on top</option>
                                    <option value="date_old_top" @If($sort=="date_old_top" ) selected @endif>Date: Oldest on top</option>
                                    <option value="price_high_to_low" @If($sort=="price_high_to_low" ) selected @endif>Price: High to low</option>
                                    <option value="price_low_to_high" @If($sort=="price_low_to_high" ) selected @endif>Price: Low to high</option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <div class="text-xs text-gray-500 flex mb-2">Seller type</div>

                                <label class="block text-gray-600 cursor-pointer"><input type="radio" value="0" class="membership" name="m" @if($membership==0) checked @endif hidden>
                                    @if($membership === "0")
                                    <strong>
                                        <div>All Ads</div>
                                    </strong>
                                    @else
                                    <div>All Ads</div>
                                    @endif
                                </label>

                                <label class="block text-gray-600 cursor-pointer"><input type="radio" value="1" class="membership" name="m" @if($membership==1) checked @endif hidden>
                                    @if($membership === "1")
                                    <strong>
                                        <div>Members Only</div>
                                    </strong>
                                    @else
                                    <div>Members Only</div>
                                    @endif
                                </label>
                            </div>

                        </div>
                        <div class="py-2 px-3">
                            <div id="accordion-flush" data-accordion="open" data-active-classes="bg-white text-gray-900" data-inactive-classes="text-gray-500">

                                {{-- Categories --}}
                                <h2 id="accordion-flush-heading-1" style="padding-bottom: 0px;">
                                    <button type="button" class="flex w-full items-center justify-between border-t border-gray-200 py-3 text-left font-medium text-gray-500 text-xs" data-accordion-target="#accordion-flush-body-1" aria-expanded="true" aria-controls="accordion-flush-body-1">
                                        <span>Category</span>
                                        <svg data-accordion-icon class="h-2 w-2 shrink-0 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                        </svg>
                                    </button>
                                </h2>
                                <div id="accordion-flush-body-1" class="hidden" aria-labelledby="accordion-flush-heading-1">
                                    <div class="border-gray-200 pb-3 pt-0 px-2 text-gray-600 text-sm">
                                        <input type="text" id="category-id" hidden readonly @if(isset($category->id)) value="{{ $category->id }}" @endif>

                                        <input type="text" id="sub-category-id" hidden readonly @if(isset($category->subId)) value="{{ $category->subId }}" @endif>

                                        @if(isset($category->id) && !empty($categories->id))
                                        <div id="clear-categories" class="mb-2 cursor-pointer">All categories</div>
                                        @else
                                        <strong>
                                            <div id="clear-categories" class="cursor-pointer mb-2">All categories</div>
                                        </strong>
                                        @endif

                                        <ol class="ml-3 mt-1">

                                            @if(isset($categories->id) && !empty($categories->id))

                                            <li>
                                                @if(isset($category->subId))
                                                <label class="cursor-pointer flex w-fit"><input type="radio" value="{{ $categories->id }}" class="category" name="category" hidden> 
                                                    <img src="{{ asset('storage/'.$categories->picture) }}" class="col lazyload img-fluid" style="height: 18px;margin-right: 8px;" alt="{{ $categories->name }} icon">
                                                    {{ $categories->name }}
                                                </label>
                                                @else
                                                <strong>
                                                    <label class="cursor-pointer flex w-fit"><input type="radio" value="{{ $categories->id }}" class="category" name="category" hidden> 
                                                        <img src="{{ asset('storage/'.$categories->picture) }}" class="col lazyload img-fluid" style="height: 18px;margin-right: 8px;" alt="{{ $categories->name }} icon">
                                                        {{ $categories->name }}
                                                    </label>
                                                </strong>

                                                <script>
                                                    document.querySelector(".categoryText").innerHTML = "{{ $categories->name }}";
                                                </script>
                                                @endif

                                                <ol class="ml-3">
                                                    @if(isset($subCategories))

                                                    @if(isset($category->subId) && !empty($category->subId))

                                                    @foreach($subCategories as $key => $subCategory)

                                                    @if($category->subId == $subCategory->id)
                                                    <li>
                                                        <strong>
                                                            <label class="cursor-pointer"><input type="radio" value="{{ $subCategory->id }}" class="subCategory" name="subCategory" hidden> {{ $subCategory->name }}</label>
                                                        </strong>
                                                    </li>

                                                    <script>
                                                        document.querySelector(".categoryText").innerHTML = "{{ $subCategory->name }}";
                                                    </script>
                                                    @endif

                                                    @endforeach

                                                    @else

                                                    @foreach($subCategories as $key => $subCategory)
                                                    <li>
                                                        <label class="cursor-pointer"><input type="radio" value="{{ $subCategory->id }}" class="subCategory" name="subCategory" hidden> {{ $subCategory->name }}</label>
                                                    </li>
                                                    @endforeach

                                                    @endif

                                                    @else
                                                    <div>No sub-categories</div>
                                                    @endif
                                                </ol>

                                            </li>

                                            @else

                                            @foreach($categories as $key => $category)

                                            <li>
                                                <label class="cursor-pointer flex w-fit"><input type="radio" value="{{ $category->id }}" class="category" name="category" hidden> 
                                                    <img src="{{ asset('storage/'.$category->picture) }}" class="col lazyload img-fluid" style="height: 18px;margin-right: 8px;" alt="{{ $category->name }} icon">
                                                    {{ $category->name }}
                                                </label>
                                            </li>

                                            @endforeach

                                            @endif
                                        </ol>

                                    </div>
                                </div>

                                {{-- Locations --}}
                                <h2 id="accordion-flush-heading-location" style="padding-bottom: 0px;">
                                    <button type="button" class="flex w-full items-center justify-between border-t border-gray-200 py-3 text-left font-medium text-gray-500 text-xs" data-accordion-target="#accordion-flush-body-location" aria-expanded="true" aria-controls="accordion-flush-body-2">
                                        <span>Location</span>
                                        <svg data-accordion-icon class="h-2 w-2 shrink-0 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                        </svg>
                                    </button>
                                </h2>
                                <div id="accordion-flush-body-location" class="hidden" aria-labelledby="accordion-flush-heading-location">
                                    <div class="border-gray-200 pb-3 pt-0 px-2 text-gray-600 text-sm">

                                        @if((isset($location->d) && !empty($location->d)) || (isset($location->c) && !empty($location->c)))
                                        <div id="clear-locations" class="mb-1 cursor-pointer">All of Sri Lanka</div>
                                        @else
                                        <strong>
                                            <div id="clear-locations" class="cursor-pointer mb-2">All of Sri Lanka</div>
                                        </strong>
                                        @endif

                                        @if(isset($districts))
                                        <ol class="ml-3 mt-1">

                                            @if((isset($location->d) && !empty($location->d)) || (isset($location->c) && !empty($location->c)))
                                            {{-- If a city or district is selected --}}

                                            <li>

                                                @if(isset($location->d) && !empty($location->d))

                                                <strong>
                                                    <label class="cursor-pointer"><input type="radio" value="{{ $districts->id }}" class="district" name="location" hidden> {{ str_replace(' District', '', $districts->name) }}</label>
                                                </strong>

                                                <script>
                                                    document.querySelector(".locationText").innerHTML = "{{ $districts->name }}";
                                                </script>

                                                @else

                                                <label class="cursor-pointer"><input type="radio" value="{{ $districts->id }}" class="district" name="location" hidden> {{ str_replace(' District', '', $districts->name) }}</label>

                                                @endif

                                                @if (isset($districts->cities))

                                                <ol class="ml-3">

                                                    @if(isset($location->c) && !empty($location->c))

                                                    @foreach($districts->cities as $key => $city)

                                                    @if($location->c == $city->id)

                                                    <strong>
                                                        <label class="block cursor-pointer"><input type="radio" value="{{ $city->id }}" class="city" name="location" hidden> {{ $city->name }}</label>
                                                    </strong>

                                                    <script>
                                                        document.querySelector(".locationText").innerHTML = "{{ $city->name }}";
                                                    </script>

                                                    @endif

                                                    @endforeach

                                                    @else

                                                    @foreach($districts->cities as $key => $city)

                                                    <label class="block cursor-pointer"><input type="radio" value="{{ $city->id }}" class="city" name="location" hidden> {{ $city->name }}</label>

                                                    @endforeach

                                                    @endif

                                                </ol>
                                                @else
                                                <div>No Cities</div>
                                                @endif

                                            </li>

                                            @else

                                            @foreach($districts as $key => $district)
                                            <li>

                                                <label class="block cursor-pointer"><input type="radio" value="{{ $district->id }}" class="district" name="location" hidden> {{ str_replace(' District', '', $district->name) }}</label>


                                            </li>
                                            @endforeach

                                            @endif

                                        </ol>
                                        @else
                                        <div>No Districts</div>
                                        @endif

                                    </div>
                                </div>

                                {{-- Price --}}
                                <h2 id="accordion-flush-heading-3" style="padding-bottom: 0px;">
                                    <button type="button" class="flex w-full items-center justify-between border-t border-gray-200 py-3 text-left font-medium text-gray-500 text-xs" data-accordion-target="#accordion-flush-body-3" aria-expanded="false" aria-controls="accordion-flush-body-3">
                                        <span>Price</span>
                                        <svg data-accordion-icon class="h-2 w-2 shrink-0 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                        </svg>
                                    </button>
                                </h2>
                                <div id="accordion-flush-body-3" class="hidden" aria-labelledby="accordion-flush-heading-3">
                                    <div class="border-gray-200 pb-3 pt-0">

                                        <div class="input-group flex">

                                            <input type="number" id="minPrice" placeholder="Min (LKR)" @if(isset($price->min)) value="{{ $price->min }}" @endif class="border col-6 p-2 text-sm">

                                            <input type="number" id="maxPrice" placeholder="Max (LKR)" @if(isset($price->max)) value="{{ $price->max }}" @endif class="border col-6 p-2 text-sm">

                                        </div>

                                        <button type="button" class="w-full mt-2 p-2 text-sm text-gray-600 bg-gray-100 border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 setPrice">Apply</button>

                                    </div>
                                </div>

                                <!-- Fields List -->
                                @include('pages.fields.container')

                            </div>
                        </div>
                    </div>

                    <div class="result-body col w-full" id="results-section">
                        <div class="py-2 px-3 result-mobile-padding">

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="contentAll" role="tabpanel" aria-labelledby="tabAll">
                                    <div id="postsList" class="category-list-wrapper posts-wrapper row no-margin">

                                        <div>
                                            <ol class="breadcrumb" style="padding-bottom: 0px;">
                                                <li class="breadcrumb-item">
                                                    <a href="{{ route('home') }}"><i class="fas fa-home"></i></a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    <a href="{{ route('search') }}"> Search </a>
                                                </li>
                                            </ol>
                                        </div>

                                        <div class="pt-1 pb-2 font-medium" style="color: #424e4e; font-size: 12px; font-weight: 400; padding-bottom: 5px;">Showing {{ $results->firstItem() }}-{{ $results->lastItem() }} of {{ $results->total() }} ads</div>

                                        <div class="col-12 col-md-9 result-mobile-padding">

                                            @include('shops.top-listing-item', ['topResults' => $topResults])

                                            @each('shops.listing-item', $results, 'post', 'shops.empty')

                                        </div>

                                        <div class="col-2 col-md-3 d-none d-md-block">

                                        </div>
                                        
                                        @if($results->hasPages())
                                            <div class="col-12 col-sm-9 flex justify-center items-center text-gray-500 my-4">
                                                <div>{{ $results->links() }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Change location modal -->
                <div id="changeLocation" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <livewire:modal.location />
                </div>

                <!-- Change category modal -->
                <div id="changeCategory" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <livewire:modal.category />
                </div>

            </form>

        </div>
    </div>

    {{-- Promo Listing Button --}}
    <div class="row mt-10 pb-4 g-3 justify-content-center">
        <div class="col-12 text-center">
            <span class="fw-bold fs-3">Do you have Something Sell or Rent?</span>
        </div>
        <div class="col-12 text-center">
            <span>Post your Free ad on Adsup.lk</span>
        </div>
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col-8 col-md-6 col-lg-4">
                    <div class="row p-2 align-items-center">
                        @if (!auth()->check() && config('settings.single.guests_can_post_listings') != '1')
                        <a class="btn btn-lg btn-listing" href="#quickLogin" data-bs-toggle="modal">
                            <i class="far fa-edit"></i> {{ t('Create Listing') }}
                        </a>
                        @else
                        <a class="btn btn-lg btn-listing ps-4 pe-4" href="{{ route('post-ad.index') }}" style="text-transform: none;">
                            <i class="far fa-edit"></i> {{ t('Create Listing') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('after_styles')

{{-- Flowbite css --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

<style>
    nav {
        z-index: 30 !important;
    }

    .btn-primary {
        background-color: var(--primary-color);
        background: var(--primary-color);
        border-color: #016fd7;
    }

    .posts-wrapper {
        overflow: auto;
        min-height: fit-content !important;
    }

    label {
        font-weight: inherit;
    }

    .item-list:last-child {
        border-bottom: none;
        cursor: pointer;
    }

    .sm\:mb-3 {
        margin-bottom: 0.75rem
    }

    .sm\:px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem
    }

    @media (min-width: 576px) {
        .sm-mx-3 {
            margin-left: 0.75rem !important;
            margin-right: 0.75rem !important
        }

        .sm\:mb-3 {
            margin-bottom: initial
        }

        .sm\:px-3 {
            padding-left: initial;
            padding-right: initial
        }
    }

    .overflow-x-hidden {
        overflow-x: hidden;
    }

    .locations-arrow:after {
        font-family: "font awesome 5 free";
        content: "\f054";
        color: #afb7ad;
        display: inline-block;
        padding-right: 3px;
        vertical-align: middle;
        float: right;
        font-weight: 900;
    }

    .img-thumbnail {
        padding: 0px;
        border-radius: 4px;
    }

    .bg-\[--primary-color\] {
        background-color: var(--primary-color)
    }

    .bg-\[--primary-color\]:hover{
        background-color: #016fd7;
    }

</style>

@endsection

@section('after_scripts')

{{-- Jquery --}}
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

{{-- Flowbite js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

{{-- Datepicker js --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

{{-- Jquery UI --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>

    $(".date-range-picker").daterangepicker();
    
    $(function() {

        $("html").addClass("overflow-x-hidden");

        $(".changeLocationButton").on("click", function() {

            $("html").removeClass("overflow-x-hidden");
        });

        $(".changeCategoryButton").on("click", function() {

            $("html").removeClass("overflow-x-hidden");
        });


        $(document).on("click", "[modal-backdrop]", function() {

            $("html").addClass("overflow-x-hidden");
        });


        $(document).on("click", "[data-modal-hide]", function() {

            $("html").addClass("overflow-x-hidden");
        });


        // When a district is selected.
        $(document).on("click", ".district", function() {

            // location : l={"d":12}

            const locationArray = {
                d: $(this).val()
            };

            let locationJsonString = JSON.stringify(locationArray);

            const encodedLocation = btoa(locationJsonString); // encode a string

            $("#location").val(encodedLocation);

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When a city is selected.
        $(document).on("click", ".city", function() {

            // location : l={"c":23}

            const locationArray = {
                c: $(this).val()
            };

            let locationJsonString = JSON.stringify(locationArray);

            const encodedLocation = btoa(locationJsonString); // encode a string

            $("#location").val(encodedLocation);

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When a category is selected.
        $(document).on("click", ".subCategory", function() {

            // category : c={"id":23, "subId":45}

            setSubCategory($(this).val());

            $("#searchForm").submit();

        });


        // When a category is selected.

        $(document).on("click", ".category", function() {

            // category : c={"id":23}

            setCategory($(this).val());

            $("#searchForm").submit();

        });

        $(document).on("click", ".category-2", function() {

            // category : c={"id":23}

            setCategory($(this).val());
        });

        function setCategory(categoryId){

            $("#category-id").val(categoryId);

            const categoryArray = {
                id: categoryId
            };

            let categoryJsonString = JSON.stringify(categoryArray);

            const encodedCategory = btoa(categoryJsonString); // encode a string

            $("#category").val(encodedCategory);

            // console.log(categoryArray);

            $("[name=subCategory]").removeAttr("name");

            $("[name=category]").removeAttr("name");
        }

        function setSubCategory(subId){

            const categoryId = $("#category-id").val();

            const categoryArray = {
                id: categoryId, 
                subId: subId,
            };

            let categoryJsonString = JSON.stringify(categoryArray);

            const encodedCategory = btoa(categoryJsonString); // encode a string

            $("#category").val(encodedCategory);

            // console.log(categoryArray);

            $("[name=subCategory]").removeAttr("name");

            $("[name=category]").removeAttr("name");
        }


        // When a `sort` is selected.
        $(".sort").change(function() {

            // sorting : s=date_new_top

            $("#searchForm").submit();

        });


        // When a select field is selected.
        $("select.field, [type=radio].field, [type=checkbox].cmfield, [type=text].field, [type=number].field, [type=url].field, [type=date].field, [type=date_range].field").change(function() {

            let value = $(this).val();

            let fieldsArray = [];

            $('.field').each(function(i, obj) {

                // Considering `field` class is only given for `radio`, `checkbox_multiple` and `select` only. (not checkbox)

                let attr = $(obj).attr("type");

                if (attr == "radio") {

                    let keyId = $(obj).attr("name");

                    fieldsArray[keyId] = ($(".field[name='" + keyId + "']:checked").val() == null) ? '' : $(".field[name='" + keyId + "']:checked").val();

                } else if (attr == "checkbox_multiple") {

                    let keyId = $(obj).attr("checkbox_multiple_name");

                    let cmval = [];

                    $(".cmfield[name='" + keyId + "']:checked").each(function(i) {

                        cmval[i] = $(this).val();

                    });

                    fieldsArray[keyId] = cmval;

                } else {

                    let keyId = $(obj).attr("name");

                    fieldsArray[keyId] = ($(obj).val() == null) ? '' : $(obj).val();

                }
            });

            // category : c={"id":1, "fields":{"1":2, "3":4, "5":[1, 2, 3]}}

            const categoryArray = {
                id: $("#category-id").val()
                , subId: $("#sub-category-id").val()
                , fields: fieldsArray
            };

            let categoryJsonString = JSON.stringify(categoryArray);

            const encodedCategory = btoa(categoryJsonString); // encode a string

            $("#category").val(encodedCategory);

            $("[name=category]").removeAttr("name");

            if (attr == "radio" || attr == "checkbox_multiple" || $(obj).is("select")){

                $("#searchForm").submit();
            }

            /*
            for (let x in fieldArray) {

                console.log(x + " - " + fieldArray[x]);

            }*/


        });


        // When `all of sri lanka` is selected.
        $("#clear-locations").on("click", function() {

            $("#location").val('');

            $("[name=location]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When `all categories` is selected.
        $("#clear-categories").on("click", function() {

            $("#category").val('');

            $("[name=c]").removeAttr("name");

            $("#searchForm").submit();

        });


        // When `all of sri lanka` is selected.
        $(".setPrice, .membership").on("click", function() {

            $("#searchForm").submit();

        });


        // When form submitted.
        $("#searchForm").on("submit", function() {

            // price : p={"min":100,"max":2300}

            if ($("#minPrice").val() == "" && $("#maxPrice").val() == "") {

                $("[name=p]").removeAttr("name");
            } else {

                const priceArray = {
                    min: $("#minPrice").val()
                    , max: $("#maxPrice").val()
                };

                let priceJsonString = JSON.stringify(priceArray);

                const encodedPrice = btoa(priceJsonString); // encode a string

                $("#price").val(encodedPrice);
            }

            if ($("#category").val() == "") {

                $("[name=c]").removeAttr("name");
            }

        });

        $("#show-filters").on("click", function() {

            $("#results-section").hide();

            $("#filters-section").removeClass("d-none");

            $("#filters-section").addClass("d-block");
        
        });

        $("#close-filters").on("click", function() {

            $("#results-section").show();

            $("#filters-section").addClass("d-none");

            $("#filters-section").removeClass("d-block");
        
        });

        $(window).resize(function(){
        
            $("html").addClass("overflow-x-hidden");

            $("#results-section").show();

            $("#filters-section").addClass("d-none");

            $("#filters-section").removeClass("d-block");
        });

    });

</script>
@endsection