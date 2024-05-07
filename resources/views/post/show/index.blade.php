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
	$post ??= [];
	$catBreadcrumb ??= [];
	$topAdvertising ??= [];
	$bottomAdvertising ??= [];
@endphp

@section('content')
	{!! csrf_field() !!}
	<input type="hidden" id="postId" name="post_id" value="{{ data_get($post, 'id') }}">
	
	@if (session()->has('flash_notification'))
		@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
		@php
			$paddingTopExists = true;
		@endphp
		<div class="container">
			<div class="row">
				<div class="col-12">
					@include('flash::message')
				</div>
			</div>
		</div>
		@php
			session()->forget('flash_notification.message');
		@endphp
	@endif
	
	{{-- Archived listings message --}}
	@if (!empty(data_get($post, 'archived_at')))
		@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
		@php
			$paddingTopExists = true;
		@endphp
		<div class="container">
			<div class="row">
				<div class="col-12" style="margin-top: 10px;">
					<div class="alert alert-warning" role="alert">
						{!! t('This listing has been archived') !!}
					</div>
				</div>
			</div>
		</div>
	@endif

	@if($isReviewing)
		<div class="container">
		    <div class="row">
		        <div class="col-12" style="margin-top: 10px;">
		            <div class="alert alert-warning" role="alert">
		                <strong>We are reviewing the ad!</strong> Please note that it can take up to {{ config('ads.default_reviewing_duration') }} for your ad to appear live on the site. You can keep track of your ad by logging in to your {{ config('app.url') }} account and checking your "My account" page.
		            </div>
		        </div>
		    </div>
		</div>
	@endif

	@if (session('giveaway_package_purchase'))
		<div class="container">
		    <div class="row">
		        <div class="col-12" style="margin-top: 10px;">
		            <div class="alert {{ session('giveaway_package_purchase')['status'] ? "alert-success" : "alert-warning" }}" role="alert">
		                {{ session('giveaway_package_purchase')['message'] }}
		            </div>
		        </div>
		    </div>
		</div>
	@endif
	
	<div class="main-container">
		
		@if (!empty($topAdvertising))
			@includeFirst(
				[config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.top', 'layouts.inc.advertising.top'],
				['paddingTopExists' => $paddingTopExists ?? false]
			)
			@php
				$paddingTopExists = false;
			@endphp
		@endif
		
		<div class="container {{ !empty($topAdvertising) ? 'mt-0' : 'mt-0' }}">
			<div class="row">
				<div class="col-md-12">
					
					<nav aria-label="breadcrumb" role="navigation" class="float-start" style=" padding-top:20px;">
						<ol class="breadcrumb">
							<li class="breadcrumb-item active"><a href="{{ route('home') }}"><i class="fas fa-home"></i></a></li>
							<li class="breadcrumb-item active"><a href="{{ route('search') }}">All Ads</a></li>
							@if (is_array($catBreadcrumb) && count($catBreadcrumb) > 0)
								@foreach($catBreadcrumb as $key => $value)
									<li class="breadcrumb-item active">
										<a href="{{ $value->get('url') }}">
											{!! $value->get('name') !!}
										</a>
									</li>
								@endforeach

							@endif
							<li class="breadcrumb-item active" aria-current="page">{{ str(data_get($post, 'title'))->limit(70) }}</li>
						</ol>
					</nav>
				
				</div>
			</div>
		</div>
		
		<div class="container">
			<div class="row bg-white" style="border-radius: 4px">
				<div class="col-lg-9 page-content col-thin-right" style="border-right: solid 1px var(--border-color);padding:0px;">
					@php
						$innerBoxStyle = (!auth()->check() && plugin_exists('reviews')) ? 'overflow: visible;' : '';
					@endphp
					<div class="inner inner-box items-details-wrapper pb-0" style="{{ $innerBoxStyle }};margin-bottom: 0px; height: 100%;">
						<h1 class="h4 fw-bold enable-long-words" style="padding-bottom: 5px;padding-top: 5px;">
							<strong>
								<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ data_get($post, 'title') }}" style="color: #000 !important;">
									{{ data_get($post, 'title') }}
                                </a>
                            </strong>
							@if (config('settings.single.show_listing_types'))
								@if (!empty(data_get($post, 'postType')))
									<small class="label label-default adlistingtype">{{ data_get($post, 'postType.name') }}</small>
								@endif
							@endif
							@if (data_get($post, 'featured') == 1 && !empty(data_get($post, 'latestPayment.package')))
								<i class="fas fa-check-circle"
								   style="color: {{ data_get($post, 'latestPayment.package.ribbon') }};"
								   data-bs-placement="bottom"
								   data-bs-toggle="tooltip"
								   title="{{ data_get($post, 'latestPayment.package.short_name') }}"
								></i>
                            @endif
						</h1>
						<span class="info-row" style="border: 0px;padding-top: 0px;">

							@if (!config('settings.single.hide_dates'))
							<span class="date"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="far fa-clock"></i> {!! data_get($post, 'created_at_formatted') !!}
							</span>&nbsp;
							@endif
							<span class="category"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="bi bi-collection"></i> {{ data_get($post, 'category.parent.name', data_get($post, 'category.name')) }}
							</span>&nbsp;
							<span class="item-location"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="bi bi-geo-alt"></i> {{ data_get($post, 'city.name') }}
							</span>&nbsp;
							{{--<span class="category"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="bi bi-eye"></i> {{
									\App\Helpers\Number::short(data_get($post, 'visits'))
									. ' '
									. trans_choice('global.count_views', getPlural(data_get($post, 'visits')), [], config('app.locale'))
									}}
							</span>--}}
							<span class="category float-md-end"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								{{ t('reference') }}: {{ hashId(data_get($post, 'id'), false, false) }}
							</span>
						</span>
						
						@include('post.show.inc.pictures-slider')
						
						@if (config('plugins.reviews.installed'))
							@if (view()->exists('reviews::ratings-single'))
								@include('reviews::ratings-single')
							@endif
						@endif
						
						@includeFirst([config('larapen.core.customizedViewPath') . 'post.show.inc.details', 'post.show.inc.details'])
					</div>
				</div>
				
				<div class="col-lg-3 page-sidebar-right" style="padding: 0px;">
					@includeFirst([config('larapen.core.customizedViewPath') . 'post.show.inc.sidebar', 'post.show.inc.sidebar'])
				</div>
			</div>

		</div>

	<style>

		@media screen and (min-width: 1084px) {
			.container {
				max-width: 1084px;
			}
		}

		.alert {
			margin-bottom: 0px;
		}

		.bg-\[--primary-color\] {
			background-color: var(--primary-color)
		}

		.bg-\[--primary-color\]:hover{
			background-color: #016fd7;
		}
	</style>
		
		@if (config('settings.single.similar_listings') == '1' || config('settings.single.similar_listings') == '2')
			@php
				$widgetType = (config('settings.single.similar_listings_in_carousel') ? 'carousel' : 'normal');
			@endphp
			@includeFirst([
					config('larapen.core.customizedViewPath') . 'search.inc.posts.widget.' . $widgetType,
					'search.inc.posts.widget.' . $widgetType
				],
				['widget' => ($widgetSimilarPosts ?? null), 'firstSection' => false]
			)
		@endif
		
		@includeFirst(
			[config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.bottom', 'layouts.inc.advertising.bottom'],
			['firstSection' => false]
		)
		
		@if (isVerifiedPost($post))
			@includeFirst(
				[config('larapen.core.customizedViewPath') . 'layouts.inc.tools.facebook-comments', 'layouts.inc.tools.facebook-comments'],
				['firstSection' => false]
			)
		@endif
		
	</div>
@endsection
@php
	if (!session()->has('emailVerificationSent') && !session()->has('phoneVerificationSent')) {
		if (session()->has('message')) {
			session()->forget('message');
		}
	}
@endphp

@section('modal_message')
	@if (config('settings.single.show_security_tips') == '1')
		@includeFirst([config('larapen.core.customizedViewPath') . 'post.show.inc.security-tips', 'post.show.inc.security-tips'])
	@endif
	@if (auth()->check() || config('settings.single.guests_can_contact_authors')=='1')
		@includeFirst([config('larapen.core.customizedViewPath') . 'account.messenger.modal.create', 'account.messenger.modal.create'])
	@endif
@endsection

@section('after_styles')
@endsection

@section('before_scripts')
	<script>
		var showSecurityTips = '{{ config('settings.single.show_security_tips', '0') }}';
	</script>
@endsection

@section('after_scripts')
    @if (config('services.googlemaps.key'))
		{{-- More Info: https://developers.google.com/maps/documentation/javascript/versions --}}
        <script async src="https://maps.googleapis.com/maps/api/js?v=weekly&key={{ config('services.googlemaps.key') }}"></script>
    @endif
    
	<script>
		{{-- Favorites Translation --}}
        var lang = {
            labelSavePostSave: "{!! t('Save listing') !!}",
            labelSavePostRemove: "{!! t('Remove favorite') !!}",
            loginToSavePost: "{!! t('Please log in to save the Listings') !!}",
            loginToSaveSearch: "{!! t('Please log in to save your search') !!}"
        };
		
		$(document).ready(function () {
			{{-- Tooltip --}}
			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[rel="tooltip"]'));
			var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl)
			});
			
			@if (config('settings.single.show_listing_on_googlemap'))
				{{--
				let mapUrl = '{{ addslashes($mapUrl) }}';
				let iframe = document.getElementById('googleMaps');
				iframe.setAttribute('src', mapUrl);
				--}}
			@endif
			
			{{-- Keep the current tab active with Twitter Bootstrap after a page reload --}}
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                /* save the latest tab; use cookies if you like 'em better: */
                /* localStorage.setItem('lastTab', $(this).attr('href')); */
				localStorage.setItem('lastTab', $(this).attr('data-bs-target'));
            });
			{{-- Go to the latest tab, if it exists: --}}
            let lastTab = localStorage.getItem('lastTab');
            if (lastTab) {
				{{-- let triggerEl = document.querySelector('a[href="' + lastTab + '"]'); --}}
				let triggerEl = document.querySelector('button[data-bs-target="' + lastTab + '"]');
				if (typeof triggerEl !== 'undefined' && triggerEl !== null) {
					let tabObj = new bootstrap.Tab(triggerEl);
					if (tabObj !== null) {
						tabObj.show();
					}
				}
            }
		});
	</script>
@endsection
