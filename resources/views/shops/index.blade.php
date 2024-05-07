@extends('layouts.master')

@php
	$apiResult ??= [];
	$apiExtra ??= [];
	$count = (array)data_get($apiExtra, 'count');
	$posts = (array)data_get($apiResult, 'data');
	$totalPosts = (int)data_get($apiResult, 'meta.total', 0);
	$tags = (array)data_get($apiExtra, 'tags');
	
	$postTypes ??= [];
	$orderByOptions ??= [];
	$displayModes ??= [];
@endphp

@section('content')

	@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.spacer', 'home.inc.spacer'])

	<div class="main-container" id="homepage">

		@if (isset($topAdvertising) && !empty($topAdvertising))
			@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.top', 'layouts.inc.advertising.top'], ['paddingTopExists' => true])
			@php
				$paddingTopExists = false;
			@endphp
		@else
			@php
				if (isset($paddingTopExists) && $paddingTopExists) {
					$paddingTopExists = false;
				}
			@endphp
		@endif

		<livewire:shop :id="$id" :username="$slug" />

		{{-- Advertising --}}
		@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.bottom', 'layouts.inc.advertising.bottom'])

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

@section('modal_location')
	@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.modal.location', 'layouts.inc.modal.location'])
@endsection

@section('after_styles')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />
	<style>
		
		.posts-wrapper {
			overflow: auto;
			min-height: fit-content !important;
		}

		html{
			overflow-x: hidden;
		}
	</style>
@endsection

@section('after_scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
	<script>
		$(document).ready(function () {
			$('#postType a').click(function (e) {
				e.preventDefault();
				var goToUrl = $(this).attr('href');
				redirect(goToUrl);
			});
			$('#orderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
		});
	</script>
@endsection
