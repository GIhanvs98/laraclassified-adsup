@extends('admin.layouts.master')

@section('header')
    <div class="row page-titles">
        <div class="col-md-5 col-12 align-self-center">
            <h2 class="mb-0">
                <span class="text-capitalize">Memberships</span>
				<small id="tableInfo">Add membership</small>
            </h2>
        </div>
        <div class="col-md-7 col-12 align-self-center d-none d-md-flex justify-content-end">
            <ol class="breadcrumb mb-0 p-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ admin_url() }}">{{ trans('admin.dashboard') }}</a></li>
				<li class="breadcrumb-item"><a href="{{ route('memberships.index') }}" class="text-capitalize">Memberships</a></li>
				<li class="breadcrumb-item active d-flex align-items-center">Add</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])

    <livewire:admin-membership-new :currencyCodes="$currencyCodes">
        
	@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.category-modal', 'post.createOrEdit.inc.category-modal'])
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
@endsection

@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.form-assets', 'post.createOrEdit.inc.form-assets'])
