@extends('admin.layouts.master')

@section('header')
<div class="row page-titles">
    <div class="col-md-6 col-12 align-self-center">
        <h2 class="mb-0">
            <span class="text-capitalize">Transactions</span>
            <small id="tableInfo">Showing results from {{ $count }} entries</small>
        </h2>
    </div>
    <div class="col-md-6 col-12 align-self-center d-none d-md-flex justify-content-end">
        <ol class="breadcrumb mb-0 p-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item active d-flex align-items-center">Transactions</li>
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

                <livewire:admin-transactions />

            </div>

        </div>

    </div>
</div>
@endsection

@section('after_styles')
<style>
    .flex {
        display: flex;
    }

    .justify-between {
        justify-content: space-between;
    }

    .align-middle {
        vertical-align: middle;
    }

</style>
@endsection

@section('after_scripts')

@endsection
