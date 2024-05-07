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
    
    <div class="container" id="post-ad-container" style="padding-top:20px;padding-bottom:20px;">
        <div class="inner-box category-content" style="margin-bottom: 0px;padding: 0px;background: transparent;">

            <div class="w-full">
                <div class="p-4 bg-orange-100 text-orange-800 border border-orange-200 rounded-md container w-fit">
                    <h3 class="font-base mb-1 text-xl">
                    `{{ ucfirst($post['title']) }}` <span class="text-sm">- {{ $post['contact_name'] }}</span>
                    </h3>
                    <span class="font-bold">We are reviewing the ad!</span> Please note that it can take up to {{ config('ads.default_reviewing_duration') }} for your ad to appear live on the site. This can be avoided if you are posting ads as a registerd user. 
                </div>
            </div>

        </div>
    </div>
    
</div>
@endsection

@section('after_styles')

    {{-- Flowbite css --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

@endsection

@section('after_scripts')

    {{-- Flowbite js --}}
    <script src="{{ asset('assets/plugins/flowbite/1.8.1/flowbite.min.js') }}"></script>

@endsection
