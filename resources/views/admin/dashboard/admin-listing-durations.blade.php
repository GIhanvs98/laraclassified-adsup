@extends('admin.layouts.master')

@section('header')
<div class="row page-titles">
    <div class="col-md-6 col-12 align-self-center">
        <h2 class="mb-0">
            <span class="text-capitalize text-xl">Listing Durations</span>
            <small id="tableInfo" class="text-sm">Showing results from {{ $count }} entries</small>
        </h2>
    </div>
    <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
        <ol class="breadcrumb mb-0 p-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item active d-flex align-items-center">Listing Durations</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card rounded">

            <div class="card-body">

                <div id="loadingData"></div>

                <livewire:admin-listing-durations />

            </div>

        </div>

    </div>
</div>
@endsection

@section('after_styles')

	{{-- Flowbite css --}}
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

	{{-- Flowbite js --}}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

    <style>
        .collapse {
            visibility: initial;
        }

        .flex {
            display: flex;
        }

        .z-\[60\]{
            z-index: 60
        }
        
        .p-5 {
            padding: 1.25rem !important;
        }
        .border-red-600{
            --tw-border-opacity: 1;
            border-color: rgb(220 38 38 / var(--tw-border-opacity)) !important
        }

        @media (min-width: 640px){
            .sm\:col-span-2{
                grid-column: span 2 / span 2
            }

            .sm\:grid-cols-2{
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        .justify-between {
            justify-content: space-between;
        }

        .align-middle {
            vertical-align: middle;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0
        }

        .relative {
            position: relative
        }

        .pt-0{
            padding-top: 0px !important
        }

        .mb-4 {
            margin-bottom: 1rem
        }

        .inline-flex {
            display: inline-flex
        }

        .h-6 {
            height: 1.5rem
        }

        .w-11 {
            width: 2.75rem
        }

        .cursor-pointer {
            cursor: pointer
        }

        .items-center {
            align-items: center
        }

        .rounded-full {
            border-radius: 9999px
        }

        .bg-gray-200 {
            --tw-bg-opacity: 1;
            background-color: rgb(229 231 235 / var(--tw-bg-opacity))
        }

        .after\:absolute::after {
            content: var(--tw-content);
            position: absolute
        }

        .after\:left-\[2px\]::after {
            content: var(--tw-content);
            left: 2px
        }

        .after\:top-0::after {
            content: var(--tw-content);
            top: 0px
        }

        .after\:top-0\.5::after {
            content: var(--tw-content);
            top: 0.125rem
        }

        .after\:h-5::after {
            content: var(--tw-content);
            height: 1.25rem
        }

        .after\:w-5::after {
            content: var(--tw-content);
            width: 1.25rem
        }

        .after\:rounded-full::after {
            content: var(--tw-content);
            border-radius: 9999px
        }

        .after\:border::after {
            content: var(--tw-content);
            border-width: 1px
        }

        .after\:border-gray-300::after {
            content: var(--tw-content);
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity))
        }

        .after\:bg-white::after {
            content: var(--tw-content);
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity))
        }

        .after\:transition-all::after {
            content: var(--tw-content);
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms
        }

        .after\:content-\[\'\'\]::after {
            --tw-content: '';
            content: var(--tw-content)
        }

        .peer:checked~.peer-checked\:bg-blue-600 {
            --tw-bg-opacity: 1;
            background-color: rgb(37 99 235 / var(--tw-bg-opacity))
        }

        .peer:checked~.peer-checked\:after\:translate-x-full::after {
            content: var(--tw-content);
            --tw-translate-x: 100%;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
        }

        .peer:checked~.peer-checked\:after\:border-white::after {
            content: var(--tw-content);
            --tw-border-opacity: 1;
            border-color: rgb(255 255 255 / var(--tw-border-opacity))
        }

        .peer:focus~.peer-focus\:ring-4 {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
        }

        .peer:focus~.peer-focus\:ring-blue-300 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(147 197 253 / var(--tw-ring-opacity))
        }

        .mr-1{
        margin-right: 0.25rem
        }

        .flex{
        display: flex
        }

        .h-4{
        height: 1rem
        }

        .w-4{
        width: 1rem
        }

        .min-w-fit{
        min-width: fit-content
        }

        .max-w-fit{
        max-width: fit-content
        }

        .items-center{
        align-items: center
        }

    </style>
@endsection

@section('after_scripts')

@endsection
