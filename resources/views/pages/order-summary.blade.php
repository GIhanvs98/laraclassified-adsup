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
    <div class="container" id="pricing" style="background: white;padding-top:40px;padding-bottom:20px;">

        <livewire:order-summary :ordertype="$order_type" :orderid="$order_id" :data="$data" />

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

    .border-gray-200 {
        --tw-border-opacity: 1;
        border-color: rgb(229 231 235 / var(--tw-border-opacity))
    }

    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem
    }

    .py-3 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem
    }

    .pl-11 {
        padding-left: 2.75rem
    }

    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem
    }

    .shadow-sm {
        --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
    }

    .outline-none {
        outline: 2px solid transparent;
        outline-offset: 2px
    }

    .focus\:z-10:focus {
        z-index: 10
    }

    .focus\:border-blue-500:focus {
        --tw-border-opacity: 1;
        border-color: rgb(59 130 246 / var(--tw-border-opacity))
    }

    .focus\:ring-blue-500:focus {
        --tw-ring-opacity: 1;
        --tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity))
    }

    .w-28{
        width: 7rem
    }

    .w-16{
        width: 4rem
    }

</style>

@endsection

@section('after_scripts')

{{-- Flowbite js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

<script>

</script>
@endsection
