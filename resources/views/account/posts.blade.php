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
	$apiResult ??= [];
	$posts = (array)data_get($apiResult, 'data');
	$totalPosts = (int)data_get($apiResult, 'meta.total', 0);
	$pagePath ??= null;
	
	$pageTitles = [
		'list' => [
			'icon'  => 'fas fa-bullhorn',
			'title' => t('my_listings'),
		],
		'archived' => [
			'icon'  => 'fas fa-calendar-times',
			'title' => t('archived_listings'),
		],
		'favourite' => [
			'icon'  => 'fas fa-bookmark',
			'title' => t('favourite_listings'),
		],
		'pending-approval' => [
			'icon'  => 'fas fa-hourglass-half',
			'title' => t('pending_approval'),
		],
	];
@endphp

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				@if (session()->has('flash_notification'))
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>

				<div class="col-md-9 page-content">

					{{--<div class="inner-box">
						<h2 class="title-2">
							<i class="{{ $pageTitles[$pagePath]['icon'] ?? 'fas fa-bullhorn' }}"></i> {{ $pageTitles[$pagePath]['title'] ?? t('posts') }}
						</h2>
						
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ url('account/posts/' . $pagePath . '/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<div class="btn-group hidden-sm" role="group">
										<button type="button" class="btn btn-sm btn-default pb-0">
											<input type="checkbox" id="checkAll" class="from-check-all">
										</button>
										<button type="button" class="btn btn-sm btn-default from-check-all">
											{{ t('Select') }}: {{ t('All') }}
										</button>
									</div>
									
									<button type="submit" class="btn btn-sm btn-default confirm-simple-action">
										<i class="fa fa-trash"></i> {{ t('Delete') }}
									</button>
									
									<div class="table-search float-end col-sm-7">
										<div class="row">
											<label class="col-5 form-label text-end">{{ t('search') }} <br>
												<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a>
											</label>
											<div class="col-7 searchpan px-3">
												<input type="text" class="form-control" id="filter">
											</div>
										</div>
									</div>
								</div>
								
								<table id="addManageTable"
									   class="table table-striped table-bordered add-manage-table table demo"
									   data-filter="#filter"
									   data-filter-text-only="true"
								>
									<thead>
										<tr>
											<th data-type="numeric" data-sort-initial="true"></th>
											<th>{{ t('Photo') }}</th>
											<th data-sort-ignore="true">{{ t('listing_details') }}</th>
											<th data-type="numeric">--</th>
											<th>{{ t('Option') }}</th>
										</tr>
									</thead>
									<tbody>
										@if (!empty($posts) && $totalPosts > 0)
											@foreach($posts as $key => $post)
												<tr>
													<td style="width:2%" class="add-img-selector">
														<div class="checkbox">
															<label><input type="checkbox" name="entries[]" value="{{ data_get($post, 'id') }}"></label>
														</div>
													</td>
													<td style="width:20%" class="add-img-td">
														<a href="{{ \App\Helpers\UrlGen::post($post) }}">
															@if(\App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->exists())
																<img class="img-thumbnail no-margin" style="border: 0px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . \App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->first()->filename) }}" alt="{{ data_get($post, 'title') }}">
															@else
																<img class="img-thumbnail no-margin" style="border: 0px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
															@endif
														</a>
													</td>
													<td style="width:52%" class="items-details-td">
														<div>
															<p>
																<strong>
																	<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ data_get($post, 'title') }}">{{ str(data_get($post, 'title'))->limit(40) }}</a>
																</strong>
																@if (in_array($pagePath, ['list', 'archived', 'pending-approval']))
																	@if (!empty(data_get($post, 'latestPayment')) && !empty(data_get($post, 'latestPayment.package')))
																		@php
																			if (data_get($post, 'featured') == 1) {
																				$color = data_get($post, 'latestPayment.package.ribbon');
																				$packageInfo = '';
																			} else {
																				$color = '#ddd';
																				$packageInfo = ' (' . t('Expired') . ')';
																			}
																		@endphp
																		<i class="fa fa-check-circle"
																			style="color: {{ $color }};"
																			data-bs-placement="bottom"
																			data-bs-toggle="tooltip"
																			title="{{ data_get($post, 'latestPayment.package.short_name') . $packageInfo }}"
																		></i>
																	@endif
																@endif
															</p>
															<p>
																<strong>
																	<i class="far fa-clock" title="{{ t('Posted On') }}"></i>
																</strong>&nbsp;{!! data_get($post, 'created_at_formatted') !!}
															</p>
															<p>
																<strong><i class="far fa-eye" title="{{ t('Visitors') }}"></i></strong> {{ data_get($post, 'visits') ?? 0 }}
																<strong><i class="bi bi-geo-alt" title="{{ t('Located In') }}"></i></strong> {{ data_get($post, 'city.name') ?? '-' }}
																<img src="{{ data_get($post, 'country_flag_url') }}" data-bs-toggle="tooltip" title="{{ data_get($post, 'country.name') }}">
															</p>
														</div>
													</td>
													<td style="width:16%" class="price-td">
														<div>
															<strong>
																{!! data_get($post, 'price_formatted') !!}
															</strong>
														</div>
													</td>
													<td style="width:10%" class="action-td">
														<div>
															@if (
																	in_array($pagePath, ['list', 'pending-approval'])
																	&& data_get($post, 'user_id') == $user->id
																	&& empty(data_get($post, 'archived_at'))
																)
																<p>
																	<a class="btn btn-warning btn-sm" href="{{ route('post-ad.edit', ['post' => $post['id'] ]) }}">

																		<i class="fa fa-edit"></i> {{ t('Edit') }}
																	</a>
																</p>
															@endif
															@if ($pagePath == 'list' && isVerifiedPost($post) && empty(data_get($post, 'archived_at')))
																<p>
																	<a class="btn btn-primary btn-sm" href="{{ route('post-ad.promote' , ['postId' => data_get($post, 'id')]) }}">
																		<i class="far fa-check-circle"></i> Promote
																	</a>
																</p>
															@endif
															@if ($pagePath == 'archived' && data_get($post, 'user_id') == $user->id && !empty(data_get($post, 'archived_at')))
																<p>
																	<a class="btn btn-info btn-sm confirm-simple-action"
																		href="{{ url('account/posts/' . $pagePath . '/' . data_get($post, 'id') . '/repost') }}"
																	>
																		<i class="fa fa-recycle"></i> {{ t('Repost') }}
																	</a>
																</p>
															@endif
															<p>
																<a class="btn btn-danger btn-sm confirm-simple-action"
																	href="{{ url('account/posts/' . $pagePath . '/' . data_get($post, 'id') . '/delete') }}"
																>
																	<i class="fa fa-trash"></i> {{ t('Delete') }}
																</a>
															</p>
														</div>
													</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</form>
						</div>
						
						<nav>
							@include('vendor.pagination.api.bootstrap-4')
						</nav>
						
					</div>--}}

					<form name="listForm" method="POST" action="{{ url('account/posts/' . $pagePath . '/delete') }}" class="relative w-full sm:rounded-sm bg-white">
						<div class="px-6 py-3 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white" style="border-radius: 0.25rem 0.25rem;">
							{!! csrf_field() !!}
							<div class="mb-2 text-xl">
								<i class="{{ $pageTitles[$pagePath]['icon'] ?? 'fas fa-bullhorn' }} pr-2"></i> {{ $pageTitles[$pagePath]['title'] ?? t('posts') }}
							</div>
							@if (!empty($posts) && $totalPosts > 0)
								<div class="py-2 flex justify-between items-center">
									<button type="submit" class="btn btn-sm btn-default confirm-simple-action">
										<i class="fa fa-trash"></i> {{ t('Delete') }}
									</button>

									<label for="filter" class="sr-only">Search</label>
									<div class="relative ml-2">
										<div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
											<svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
										</div>
										<input type="text" id="filter" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for ads">
									</div>
								</div>
							@endif
						</div>

						@if (empty($posts) && $totalPosts <= 0)
							<div class="px-6 pb-3 text-left text-xs text-gray-600">
								You don't have any <span class="lowercase">{{ $pageTitles[$pagePath]['title'] ?? t('posts') }}</span>.
							</div>
						@else
							<div class="overflow-x-auto w-full" style="border-radius: 0.25rem 0.25rem;">
								<table class="w-full text-sm text-left rtl:text-right text-gray-500"  id="addManageTable" data-filter="#filter" data-filter-text-only="true">
									<thead class="text-xs text-gray-700 uppercase bg-gray-50">
										<tr>
											<th scope="col" class="p-3" data-type="numeric" data-sort-initial="true">
												<div class="flex items-center">
													<input type="checkbox" id="checkAll" class="from-check-all w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
													<label for="checkAll" class="sr-only">checkbox</label>
												</div>
											</th>
											<th scope="col" class="p-3">
												{{ t('Photo') }}
											</th>
											<th scope="col" class="p-3" data-sort-ignore="true">
												{{ t('listing_details') }}
											</th>
											<th scope="col" class="p-3" data-type="numeric">
												Price
											</th>
											<th scope="col" class="p-3">
												<span class="sr-only">{{ t('Option') }}</span>
											</th>
										</tr>
									</thead>
									<tbody>
										@foreach($posts as $key => $post)
										
											@php 
												$reviewingViolation = App\Models\PostReviewingViolation::where('post_id', $post['id'])->timeleft()->first();
											@endphp

											@if (in_array($pagePath, ['pending-approval']) && $reviewingViolation)
												<tr class="bg-white border-l-5 border-red-600">
													<td class="" class="p-3"></td>
													<td colspan="4" class="p-3 border-b border-gray-100">
														<span class="font-semibold">Reason</span>: {{ $reviewingViolation->reason }}
													</td>
												</tr>
												<tr class="bg-white border-l-5 border-red-600">
													<td scope="row" class="p-3">
														<div class="flex items-center">
															<input name="entries[]" value="{{ data_get($post, 'id') }}" id="select_post_{{ data_get($post, 'id') }}" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
															<label for="select_post_{{ data_get($post, 'id') }}" class="sr-only">checkbox</label>
														</div>
													</td>
													<td class="p-3">
														<a href="{{ \App\Helpers\UrlGen::post($post) }}">
															@if(\App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->exists())
																<img class="img-thumbnail no-margin" style="border: 0px; min-width: 130px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . \App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->first()->filename) }}" alt="{{ data_get($post, 'title') }}">
															@else
																<img class="img-thumbnail no-margin" style="border: 0px; min-width: 130px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
															@endif
														</a>
													</td>
													<td class="p-3 min-w-max">
														<div>
															<p class="font-base mb-2">
																<strong>
																	<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ data_get($post, 'title') }}">{{ str(data_get($post, 'title'))->limit(40) }}</a>
																</strong>
															</p>
															<p class="mb-1">
																<strong>
																	<i class="far fa-clock" title="{{ t('Posted On') }}"></i>
																</strong>&nbsp;{!! data_get($post, 'created_at_formatted') !!}
															</p>
															<p>
																{{--<strong><i class="far fa-eye" title="{{ t('Visitors') }}"></i></strong> {{ data_get($post, 'visits') ?? 0 }}--}}
																<strong><i class="bi bi-geo-alt" title="{{ t('Located In') }}"></i></strong> {{ data_get($post, 'city.name') ?? '-' }}
																{{--<img src="{{ data_get($post, 'country_flag_url') }}" data-bs-toggle="tooltip" title="{{ data_get($post, 'country.name') }}">--}}
															</p>
														</div>
													</td>
													<td class="p-3">
														<div>
															<strong>
																Rs.{{ number_format($post['price'], 0, '', ',') }}
																<small style="font-size: 12px;background-color: transparent !important;font-weight: 400;">
																	@if(isset($post['price_unit']))
																		{{ ucfirst(reverse_slug($post['price_unit'])) }}
																	@endif
																</small>
															</strong>
														</div>
													</td>
													<td class="p-3 text-left">
														<div>
															<p class="py-1">
																<a class="btn btn-warning btn-sm" href="{{ route('post-ad.edit', ['post' => $post['id'] ]) }}">
																	<i class="fa fa-edit"></i> {{ t('Edit') }}
																</a>
															</p>
															<p class="py-1">
																<a class="btn btn-danger btn-sm confirm-simple-action" href="{{ url('account/posts/' . $pagePath . '/' . data_get($post, 'id') . '/delete') }}">
																	<i class="fa fa-trash"></i> {{ t('Delete') }}
																</a>
															</p>
														</div>
													</td>
												</tr>
												<tr class="bg-white border-l-5 border-red-600">
													<td class="" class="p-3"></td>
													<td colspan="4" class="p-3 border-t border-gray-100">
														@if($reviewingViolation->rechecked_datetime)
															<div class="font-semibold flex justify-end items-center text-green-600">
																<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
																	<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
																</svg>
																<div class="ml-1">
																	Your edits are currenly validating.
																</div>
															</div>
														@else
															<div class="font-semibold flex justify-end items-center text-red-600">
																<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
																	<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
																</svg>
																<div class="ml-1">
																	{{ Carbon\Carbon::now()->settings(['monthOverflow' => false])->diffForHumans($reviewingViolation->last_datetime, ['syntax' => Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
																	left to edit this ad!
																</div>
															</div>
														@endif
													</td>
												</tr>
											@else
												<tr class="bg-white border-b">
													<td scope="row" class="p-3">
														<div class="flex items-center">
															<input name="entries[]" value="{{ data_get($post, 'id') }}" id="select_post_{{ data_get($post, 'id') }}" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
															<label for="select_post_{{ data_get($post, 'id') }}" class="sr-only">checkbox</label>
														</div>
													</td>
													<td class="p-3">
														<a href="{{ \App\Helpers\UrlGen::post($post) }}">
															@if(\App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->exists())
																<img class="img-thumbnail no-margin" style="border: 0px; min-width: 130px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . \App\Models\ThumbnailImage::where('post_id', data_get($post, 'id'))->first()->filename) }}" alt="{{ data_get($post, 'title') }}">
															@else
																<img class="img-thumbnail no-margin" style="border: 0px; min-width: 130px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
															@endif
														</a>
													</td>
													<td class="p-3 min-w-max">
														<div>
															<p class="font-base mb-2">
																<strong>
																	<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ data_get($post, 'title') }}">{{ str(data_get($post, 'title'))->limit(40) }}</a>
																</strong>
																@if (in_array($pagePath, ['list', 'archived', 'pending-approval']))
																	@if (!empty(data_get($post, 'latestPayment')) && !empty(data_get($post, 'latestPayment.package')))
																		@php
																			if (data_get($post, 'featured') == 1) {
																				$color = data_get($post, 'latestPayment.package.ribbon');
																				$packageInfo = '';
																			} else {
																				$color = '#ddd';
																				$packageInfo = ' (' . t('Expired') . ')';
																			}
																		@endphp
																		<i class="fa fa-check-circle"
																			style="color: {{ $color }};"
																			data-bs-placement="bottom"
																			data-bs-toggle="tooltip"
																			title="{{ data_get($post, 'latestPayment.package.short_name') . $packageInfo }}"
																		></i>
																	@endif
																@endif
															</p>
															<p class="mb-1">
																<strong>
																	<i class="far fa-clock" title="{{ t('Posted On') }}"></i>
																</strong>&nbsp;{!! data_get($post, 'created_at_formatted') !!}
															</p>
															<p>
																{{--<strong><i class="far fa-eye" title="{{ t('Visitors') }}"></i></strong> {{ data_get($post, 'visits') ?? 0 }}--}}
																<strong><i class="bi bi-geo-alt" title="{{ t('Located In') }}"></i></strong> {{ data_get($post, 'city.name') ?? '-' }}
																{{--<img src="{{ data_get($post, 'country_flag_url') }}" data-bs-toggle="tooltip" title="{{ data_get($post, 'country.name') }}">--}}
															</p>
														</div>
													</td>
													<td class="p-3">
														<div>
															<strong>
																Rs.{{ number_format($post['price'], 0, '', ',') }}
																<small style="font-size: 12px;background-color: transparent !important;font-weight: 400;">
																	@if(isset($post['price_unit']))
																		{{ ucfirst(reverse_slug($post['price_unit'])) }}
																	@endif
																</small>
															</strong>
														</div>
													</td>
													<td class="p-3 text-left">
														<div>
															@if (
																	in_array($pagePath, ['list', 'pending-approval'])
																	&& data_get($post, 'user_id') == $user->id
																	&& empty(data_get($post, 'archived_at'))
																)
																<p class="py-1">
																	<a class="btn btn-warning btn-sm" href="{{ route('post-ad.edit', ['post' => $post['id'] ]) }}">

																		<i class="fa fa-edit"></i> {{ t('Edit') }}
																	</a>
																</p>
															@endif
															@if ($pagePath == 'list' && isVerifiedPost($post) && empty(data_get($post, 'archived_at')))
																<p class="py-1">
																	<a class="btn btn-primary btn-sm" href="{{ route('post-ad.promote' , ['postId' => data_get($post, 'id')]) }}">
																		<i class="far fa-check-circle"></i> Promote
																	</a>
																</p>
															@endif
															@if ($pagePath == 'archived' && data_get($post, 'user_id') == $user->id && !empty(data_get($post, 'archived_at')))
																<p class="py-1">
																	<a class="btn btn-info btn-sm confirm-simple-action"
																		href="{{ url('account/posts/' . $pagePath . '/' . data_get($post, 'id') . '/repost') }}"
																	>
																		<i class="fa fa-recycle"></i> {{ t('Repost') }}
																	</a>
																</p>
															@endif
															<p class="py-1">
																<a class="btn btn-danger btn-sm confirm-simple-action"
																	href="{{ url('account/posts/' . $pagePath . '/' . data_get($post, 'id') . '/delete') }}"
																>
																	<i class="fa fa-trash"></i> {{ t('Delete') }}
																</a>
															</p>
														</div>
													</td>
												</tr>
											@endif
										@endforeach
									</tbody>
								</table>
								<nav>
									@include('vendor.pagination.api.bootstrap-4')
								</nav>
							</div>
						@endif

					</form>

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
		.action-td p {
			margin-bottom: 5px;
		}

		.collapse {
			visibility: initial;
		}

		.sr-only{
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

		.pointer-events-none{
		pointer-events: none
		}

		.absolute{
		position: absolute
		}

		.relative{
		position: relative
		}

		.inset-y-0{
		top: 0px;
		bottom: 0px
		}

		.left-0{
		left: 0px
		}

		.mb-2{
		margin-bottom: 0.5rem
		}

		.block{
		display: block
		}

		.flex{
		display: flex
		}

		.table{
		display: table
		}

		.h-4{
		height: 1rem
		}

		.h-5{
		height: 1.25rem
		}

		.w-4{
		width: 1rem
		}

		.w-5{
		width: 1.25rem
		}

		.w-80{
		width: 20rem
		}

		.w-full{
		width: 100%
		}

		.items-center{
		align-items: center
		}

		.justify-between{
		justify-content: space-between
		}

		.overflow-x-auto{
		overflow-x: auto
		}

		.rounded{
		border-radius: 0.25rem
		}

		.rounded-lg{
		border-radius: 0.5rem
		}

		.border{
		border-width: 1px
		}

		.border-b{
		border-bottom-width: 1px
		}

		.border-gray-300{
		--tw-border-opacity: 1;
		border-color: rgb(209 213 219 / var(--tw-border-opacity))
		}

		.bg-gray-100{
		--tw-bg-opacity: 1;
		background-color: rgb(243 244 246 / var(--tw-bg-opacity))
		}

		.bg-gray-50{
		--tw-bg-opacity: 1;
		background-color: rgb(249 250 251 / var(--tw-bg-opacity))
		}

		.bg-white{
		--tw-bg-opacity: 1;
		background-color: rgb(255 255 255 / var(--tw-bg-opacity))
		}

		.p-2{
		padding: 0.5rem
		}

		.px-3{
		padding-left: 0.75rem;
		padding-right: 0.75rem
		}

		.px-6{
		padding-left: 1.5rem;
		padding-right: 1.5rem
		}

		.py-1{
		padding-top: 0.25rem;
		padding-bottom: 0.25rem
		}

		.py-2{
		padding-top: 0.5rem;
		padding-bottom: 0.5rem
		}

		.py-3{
		padding-top: 0.75rem;
		padding-bottom: 0.75rem
		}

		.pr-2{
		padding-right: 0.5rem
		}

		.ps-10{
		padding-inline-start: 2.5rem !important
		}

		.ps-3{
		padding-inline-start: 0.75rem
		}

		.text-left{
		text-align: left
		}

		.text-lg{
		font-size: 1.125rem;
		line-height: 1.75rem
		}

		.text-sm{
		font-size: 0.875rem;
		line-height: 1.25rem
		}

		.text-xl{
		font-size: 1.25rem;
		line-height: 1.75rem
		}

		.text-xs{
		font-size: 0.75rem;
		line-height: 1rem
		}

		.font-semibold{
		font-weight: 600
		}

		.uppercase{
		text-transform: uppercase
		}

		.text-blue-600{
		--tw-text-opacity: 1;
		color: rgb(37 99 235 / var(--tw-text-opacity))
		}

		.text-gray-500{
		--tw-text-opacity: 1;
		color: rgb(107 114 128 / var(--tw-text-opacity))
		}

		.text-gray-700{
		--tw-text-opacity: 1;
		color: rgb(55 65 81 / var(--tw-text-opacity))
		}

		.text-gray-900{
		--tw-text-opacity: 1;
		color: rgb(17 24 39 / var(--tw-text-opacity))
		}

		.filter{
		filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
		}

		.focus\:border-blue-500:focus{
		--tw-border-opacity: 1;
		border-color: rgb(59 130 246 / var(--tw-border-opacity))
		}

		.focus\:ring-blue-500:focus{
		--tw-ring-opacity: 1;
		--tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity))
		}

		@media (min-width: 640px){
		.sm\:rounded-sm{
			border-radius: 0.25rem
		}
		}

		.rtl\:right-0:where([dir="rtl"], [dir="rtl"] *){
		right: 0px
		}

		.rtl\:text-right:where([dir="rtl"], [dir="rtl"] *){
		text-align: right
		}

		.border-l-5{
		border-left-width: 5px
		}

		.border-red-600{
		--tw-border-opacity: 1;
		border-color: rgb(220 38 38 / var(--tw-border-opacity))
		}

		.border-gray-100{
		--tw-border-opacity: 1;
		border-color: rgb(243 244 246 / var(--tw-border-opacity))
		}

		.border-gray-200{
		--tw-border-opacity: 1;
		border-color: rgb(229 231 235 / var(--tw-border-opacity))
		}

	</style>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				let selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});

			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});

			$('.from-check-all').click(function () {
				checkAll(this);
			});
		});
	</script>
	{{-- include custom script for listings table [select all checkbox]  --}}
	<script>
		function checkAll(bx) {
			if (bx.type !== 'checkbox') {
				bx = document.getElementById('checkAll');
				bx.checked = !bx.checked;
			}
			
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type === 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection
