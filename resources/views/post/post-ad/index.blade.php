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
        <div class="container" id="post-ad-container" style="background: white;padding-top:40px;padding-bottom:20px;border-radius: 4px;">
            <div class="inner-box category-content bg-white">
    
                <!-- To Sell something or Looking for Buy/Rent: -->
                <h2 class="title-2 text-center text-gray-700  text-2xl border-0 mt-[40px] font-light">
                    <strong>@if (auth()->check())Dear {{ auth()->user()->name }},@endif Please select the correct option below to post your ad</strong>
                </h2>

                <div class="h-fit bg grid sm:grid-cols-3 grid-cols-1">

                    @forelse($categoryGroups as $categoryGroup)
                        <div class="w-full">
                            <div class="m-4 p-6 border border-gray-200 border-solid rounded-sm">

                                <h3 class="w-full flex items-center justify-center text-xl">
                                    {!! $categoryGroup->icon !!}
                                    <span>{{ $categoryGroup->name }}</span>
                                </h3>

                                @forelse($categoryGroup->subCategoryGroups as $subCategoryGroup)
                                    <a href="{{ route('post-ad.main-category', ['mainCategory' => $subCategoryGroup->id ]) }}" class="hover:cursor-pointer my-2 py-2 flex justify-between text-blue-600 h-full">
                                        <span>{{ $subCategoryGroup->name }}</span>
                                        <svg class="w-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                                        </svg>
                                    </a>
                                @empty
                                    <p class="hover:cursor-pointer my-2 py-2 flex justify-between text-blue-600 h-full">
                                        <span>No sub category.</span>
                                    </p>
                                @endforelse
                                
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center sm:col-span-3 col-span-1">
                            No category list.
                        </div>
                    @endforelse

                </div>

                <div class="flex w-full">
                    <div class="reg-sidebar-inner text-center w-full sm:flex block" style="margin-top: 40px">

                        {{-- Create Form --}}
                        <div class="promo-text-box sm:w-1/2 w-full">
                            <i class="far fa-image fa-4x icon-color-1"></i>
                            <h3><strong>{{ t('create_new_listing') }}</strong></h3>
                            <p>
                                {{ t('do_you_have_something_text', ['appName' => config('app.name')]) }}
                            </p>
                        </div>

                        <div class="card sidebar-card border-color-primary sm:w-1/2 w-full">
                            <div class="card-header bg-primary border-color-primary text-white uppercase">
                                <strong>{{ t('how_to_sell_quickly') }}</strong>
                            </div>
                            <div class="card-content">
                                <div class="card-body text-start">
                                    <ul class="list-check">
                                        <li> {{ t('sell_quickly_advice_1') }} </li>
                                        <li> {{ t('sell_quickly_advice_2') }}</li>
                                        <li> {{ t('sell_quickly_advice_3') }}</li>
                                        <li> {{ t('sell_quickly_advice_4') }}</li>
                                        <li> {{ t('sell_quickly_advice_5') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<style>
.block{
display: block
}

.flex{
display: flex
}

.w-full{
width: 100%
}

.text-center{
text-align: center
}

.text-start{
text-align: start
}

.uppercase{
text-transform: uppercase
}

.text-white{
--tw-text-opacity: 1;
color: rgb(255 255 255 / var(--tw-text-opacity))
}

@media (min-width: 640px){
.sm\:flex{
display: flex
}

.sm\:w-1\/2{
width: 50%
}
}

</style>
@endsection

@section('after_scripts')
<script>

</script>
@endsection

