@extends('admin.layouts.master')

@section('header')
<div class="row page-titles">
    <div class="col-md-6 col-12 align-self-center">
        <h2 class="mb-0">
            <span class="text-capitalize">{{ $district->name }} <i class="bi bi-arrow-right"></i> Cities</span>
            <small id="tableInfo">Reorder</small>
        </h2>
    </div>
    <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
        <ol class="breadcrumb mb-0 p-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.locations.cities') }}" class="text-capitalize">Cities</a></li>
            <li class="breadcrumb-item active d-flex align-items-center">Reorder</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="controlls" style="display: flex; margin-bottom: 20px;">

            <a href="{{ route('admin.locations.districts') }}" class="btn btn-primary shadow" style="margin-right: 5px;">
                <i class="fa fa-angle-double-left"></i> Back to all districts
            </a>

            <a href="{{ route('admin.locations.cities.reorder', ['districtId' => $district->id ]) }}?order=a-z" title="Sort A to Z" style="margin-right: 5px;" class="bulk-action btn btn-outline-secondary shadow">
                <i class="bi bi-sort-alpha-down"></i>
            </a>

            <a href="{{ route('admin.locations.cities.reorder', ['districtId' => $district->id ]) }}?order=z-a" title="Sort Z to A" style="margin-right: 5px;" class="bulk-action btn btn-outline-secondary shadow">
                <i class="bi bi-sort-alpha-down-alt"></i>
            </a>

            <a href="{{ route('admin.locations.cities.reorder', ['districtId' => $district->id ]) }}?order=1-9" title="Sort 1 to 9" style="margin-right: 5px;" class="bulk-action btn btn-outline-secondary shadow">
                <i class="bi bi-sort-numeric-down"></i>
            </a>

            <a href="{{ route('admin.locations.cities.reorder', ['districtId' => $district->id ]) }}?order=9-1" title="Sort 9 to 1" style="margin-right: 5px;" class="bulk-action btn btn-outline-secondary shadow">
                <i class="bi bi-sort-numeric-down-alt"></i>
            </a>

        </div>

        <div class="card rounded">

            <div class="card-body">

                <div id="loadingData"></div>

                <p>Use drag &amp; drop to reorder.</p>

                <div class="card text-white bg-info rounded mb-0">
                    <div class="card-body">
                        NOTE: The app update every city as a loop of request in-order to re-arrange the cities. This request could therefore take a little time to process.
                    </div>
                </div>

                <livewire:locations.cities-reorder :order="$order" :district="$district->id" />

            </div>

        </div>

    </div>
</div>
@endsection

@section('after_styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>

    .mr-3 {
        margin-right: 0.75rem
    }

    .inline {
        display: inline
    }

    .h-4 {
        height: 1rem
    }

    .w-4 {
        width: 1rem
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite
    }

    .text-white {
        --tw-text-opacity: 1;
        color: rgb(255 255 255 / var(--tw-text-opacity))
    }

    .flex {
        display: flex;
    }

    .justify-between {
        justify-content: space-between;
    }

    .align-middle {
        vertical-align: middle;
    }

    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    #sortable li {
        margin: 0 3px 5px 3px;
        padding: 0.4em;
        padding-left: 1em;
        font-size: 1em;
        cursor: move;
        border: 1px solid #ddd;
        border-radius: 3px;
        height: 35px;
    }

    .table-search{
        margin-bottom: 15px;
    }
    
    label{
        margin-bottom: 0px !important;
    }
    
</style>
@endsection

@section('after_scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

@endsection
