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

	<div class="main-container inner-page pb-0">
		<div class="container bg-white pt-16 pb-20" id="pricing">

			<div class="mx-auto max-w-screen-md text-center mb-1 lg:mb-12 px-4 sm:px-6 lg:px-8">
				<div class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900">
					Choose your Membership
				</div>
				<div class="mb-1 font-light text-gray-500 sm:text-lg">
					The memberships help sellers to promote their products or services by giving more visibility to their listings to attract more buyers and sell faster. and to create more than 5 listings per for every category.
				</div>
			</div>

			<div class="w-full px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16 pt-5">
				<div class="grid grid-cols-1 gap-4 sm:items-stretch md:grid-cols-3 md:gap-8">

					@forelse($memberships as $key => $membership)
						<div class="divide-y divide-gray-200 rounded-2xl border border-gray-200 shadow-sm">
							<div class="p-6 sm:px-8">
								<h2 class="text-lg font-medium text-gray-900">
									{{ $membership->name }}
									<span class="sr-only">Plan</span>
								</h2>

								<p class="mt-2 text-gray-700">
									{{ $membership->description }}
								</p>

								<p class="mt-2 sm:mt-4">
									<strong class="text-3xl font-bold text-gray-900 sm:text-4xl">
										{{ $membership->currency->symbol }}.{{ $membership->amount }}
									</strong>
									<span class="text-sm font-medium text-gray-700">/month</span>
								</p>

								<a class="mt-4 block rounded border border-blue-600 px-12 py-3 text-center text-sm font-medium text-white" style="background-color: var(--primary-color);" href="{{ route('memberships.checkout', ['order_type' => 'memberships', 'order_id' => $membership->id ]) }}">
									Get Started
								</a>
							</div>
							<div class="p-6 sm:px-8">
								<p class="text-lg font-medium text-gray-900 sm:text-xl">What's included:</p>

								<ul class="mt-2 space-y-2 sm:mt-4">
									
									<li class="flex items-center gap-1">
										<svg
										xmlns="http://www.w3.org/2000/svg"
										fill="none"
										viewBox="0 0 24 24"
										stroke-width="1.5"
										stroke="currentColor"
										class="h-5 w-5 text-blue-700"
										>
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
										</svg>

										<span class="text-gray-700"> {{ $membership->allowed_ads }} ads {{ $membership->allowed_ads_category_rate == "per_main_category" ? "per main category" : "" }}</span>
									</li>

									@if($membership->giveaway_packages)
										@foreach (json_decode($membership->giveaway_packages, false) as $package)
											<li class="flex items-center gap-1">
												<svg
												xmlns="http://www.w3.org/2000/svg"
												fill="none"
												viewBox="0 0 24 24"
												stroke-width="1.5"
												stroke="currentColor"
												class="h-5 w-5 text-blue-700"
												>
												<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
												</svg>
												@php
													$promotion = App\Models\Package::find($package->id);
												@endphp
												<span class="text-gray-700"> {{ $package->count }} {{ $promotion ? $promotion->name : "Unknown" }} for {{ $promotion ? $promotion->duration : "unknown" }} days </span>
											</li>
										@endforeach
									@endif

									<li class="flex items-center gap-1">
										<svg
										xmlns="http://www.w3.org/2000/svg"
										fill="none"
										viewBox="0 0 24 24"
										stroke-width="1.5"
										stroke="currentColor"
										class="h-5 w-5 text-blue-700"
										>
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
										</svg>

										<span class="text-gray-700"> Whatsapp & call support </span>
									</li>

									<li class="flex items-center gap-1">
										@if($membership->post_untill_membership_duration)
											<svg
												xmlns="http://www.w3.org/2000/svg"
												fill="none"
												viewBox="0 0 24 24"
												stroke-width="1.5"
												stroke="currentColor"
												class="h-5 w-5 text-blue-700"
											>
												<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
											</svg>
										@else
											<svg
												xmlns="http://www.w3.org/2000/svg"
												fill="none"
												viewBox="0 0 24 24"
												stroke-width="1.5"
												stroke="currentColor"
												class="h-5 w-5 text-red-700"
											>
												<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
											</svg>
										@endif	

										<span class="text-gray-700"> Unlimited ad duration </span>
									</li>

								</ul>
							</div>
						</div>
					@empty
						<div class="col-md-6 col-sm-12 text-center">
							<div class="card bg-light">
								<div class="card-body">
									Memberships not found.
								</div>
							</div>
						</div>
					@endforelse

				</div>
			</div>

			@if($nonMembership)
				<div class="w-full px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16 pt-1">
					<div class="rounded-lg border bg-gray-50 p-6 sm:px-8">
						<div class="sm:flex">
						<div class="w-full">
							<div class="mb-2 text-xs text-gray-700">We also have the</div>
							<h2 class="text-lg font-medium text-gray-900">
							Free membership
							<span class="sr-only">Plan</span>
							</h2>

							<p class="mt-2 text-gray-700">If you want to try out our ad platform. This can be a good oppertunity. Sign up now!</p>
						</div>
						<div class="flex min-w-max items-center justify-center sm:ml-3">
							<a class="block w-full min-w-full rounded border border-blue-600 bg-blue-600 px-4 py-3 text-center text-sm font-medium text-white" href="{{ route('register') }}"> Get Started</a>
						</div>
						</div>

						<div class="pt-3">
						<p class="text-sm font-medium text-gray-900 sm:text-base">What's included:</p>

						<ul class="mt-2 space-y-2 sm:mt-4">
							
							<li class="flex items-center gap-1">
								<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="h-5 w-5 text-blue-700"
								>
								<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
								</svg>

								<span class="text-gray-700"> {{ $nonMembership->allowed_ads }} ads {{ $nonMembership->allowed_ads_category_rate == "per_main_category" ? "per main category" : "" }}</span>
							</li>

							@if($nonMembership->giveaway_packages)
								@foreach (json_decode($nonMembership->giveaway_packages, false) as $package)
									<li class="flex items-center gap-1">
										<svg
										xmlns="http://www.w3.org/2000/svg"
										fill="none"
										viewBox="0 0 24 24"
										stroke-width="1.5"
										stroke="currentColor"
										class="h-5 w-5 text-blue-700"
										>
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
										</svg>
										@php
											$promotion = App\Models\Package::find($package->id);
										@endphp
										<span class="text-gray-700"> {{ $package->count }} {{ $promotion ? $promotion->name : "Unknown" }} for {{ $promotion ? $promotion->duration : "unknown" }} days </span>
									</li>
								@endforeach
							@endif

							<li class="flex items-center gap-1">
								<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="h-5 w-5 text-blue-700"
								>
								<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
								</svg>

								<span class="text-gray-700"> Whatsapp & call support </span>
							</li>

							<li class="flex items-center gap-1">
								@if($nonMembership->post_untill_membership_duration)
									<svg
										xmlns="http://www.w3.org/2000/svg"
										fill="none"
										viewBox="0 0 24 24"
										stroke-width="1.5"
										stroke="currentColor"
										class="h-5 w-5 text-blue-700"
									>
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
									</svg>
								@else
									<svg
										xmlns="http://www.w3.org/2000/svg"
										fill="none"
										viewBox="0 0 24 24"
										stroke-width="1.5"
										stroke="currentColor"
										class="h-5 w-5 text-red-700"
									>
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
									</svg>
								@endif	

								<span class="text-gray-700"> Unlimited ad duration </span>
							</li>

						</ul>
						</div>
					</div>
				</div>
			@endif
			
		</div>
	</div>
@endsection


@section('after_styles')

	{{-- Flowbite css --}}
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

	{{-- Flowbite js --}}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

	<style>
		footer.main-footer{
			margin-top: -4px !important;
		}
	</style>

@endsection

@section('after_scripts')
@endsection
