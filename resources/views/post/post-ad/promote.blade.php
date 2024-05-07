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
    <div class="container" id="post-ad-container" style="background: white;padding-top:20px;padding-bottom:20px;border-radius: 4px;">
        <div class="inner-box category-content bg-white" style="margin-bottom: 0px;padding-bottom: inherit;">

            <livewire:post-ad.promote :postId="$postId" />

        </div>
    </div>
</div>
@endsection

@section('after_styles')

{{-- Flowbite css --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

<style>
    .btn-primary {
        background-color: var(--primary-color);
        background: var(--primary-color);
        border-color: #016fd7;
    }

</style>

@endsection

@section('after_scripts')

{{-- Flowbite js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

<script>

</script>
@endsection
