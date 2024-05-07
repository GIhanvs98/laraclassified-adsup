
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

            <livewire:post-ad.edit.details :post="$postId" />

        </div>
    </div>
</div>
@endsection

@section('after_styles')

    {{-- Datepicker css --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- Flowbite css --}}
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />
        
    {{-- Jquery UI --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        .btn-primary {
            background-color: var(--primary-color);
            background: var(--primary-color);
            border-color: #016fd7;
        }

        .tox-tinymce {
            border: 1px solid #dee2e6 !important;
            border-radius: 0px !important;
        }

        .sortable-image-item {
            border: 1px solid #e5e5e5;
        }

        .sortable-image-item:hover {
            cursor: move;
        }

        .tox:not(.tox-tinymce-inline) .tox-editor-header{
            box-shadow: none !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        @media screen and (min-width: 760px) {
            #post-ad-container div.inner-box {
                max-width: 760px;
            }
        }
    </style>

@endsection

@section('after_scripts')

    {{-- Datepicker js --}}
    <script src="{{ asset('assets/plugins/jquery/3.3.1/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/momentjs/2.18.1/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/3.14.1/daterangepicker.min.js') }}"></script>

    {{-- Flowbite js --}}
    <script src="{{ asset('assets/plugins/flowbite/1.8.1/flowbite.min.js') }}"></script>

    {{-- Tinymce --}}
    <script src="https://cdn.tiny.cloud/1/090tcphbqgz9ryhjyh84bx4c6kf9sum2yht4lmy7euln5dyx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    {{-- Jquery UI --}}
    <script src="{{ asset('assets/plugins/jqueryui/1.13.2/jquery-ui.js') }}"></script>

    {{-- PixelHatch --}}
    <script src="{{ asset('assets/plugins/PixelHatch/1.0.0/PixelHatch.js') }}"></script>

    {{-- JS Timer --}}
    <script src="{{ asset('assets/plugins/js-interval/cdn/interval.js') }}"></script>

    {{-- He.js plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/he/1.2.0/he.min.js" integrity="sha512-PEsccDx9jqX6Dh4wZDCnWMaIO3gAaU0j46W//sSqQhUQxky6/eHZyeB3NrXD2xsyugAKd4KPiDANkcuoEa2JuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script>
        $(".date-range-picker").daterangepicker();
	</script>

@endsection