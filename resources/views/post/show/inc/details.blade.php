@php
	$post ??= [];
@endphp
<div class="items-details">
	<!--ul class="nav nav-tabs" id="itemsDetailsTabs" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active"
					id="item-details-tab"
					data-bs-toggle="tab"
					data-bs-target="#item-details"
					role="tab"
					aria-controls="item-details"
					aria-selected="true"
			>
				<h4>{{ t('listing_details') }}</h4>
			</button>
		</li>
		@if (config('plugins.reviews.installed'))
			<li class="nav-item" role="presentation">
				<button class="nav-link"
						id="item-{{ config('plugins.reviews.name') }}-tab"
						data-bs-toggle="tab"
						data-bs-target="#item-{{ config('plugins.reviews.name') }}"
						role="tab"
						aria-controls="item-{{ config('plugins.reviews.name') }}"
						aria-selected="false"
				>
					<h4>
						{{ trans('reviews::messages.Reviews') }} ({{ data_get($post, 'rating_count', 0) }})
					</h4>
				</button>
			</li>
		@endif
	</ul-->
	
	{{-- Tab panes --}}
	<div class="tab-content p-3 mb-3" id="itemsDetailsTabsContent">
		<div class="tab-pane show active" id="item-details" role="tabpanel" aria-labelledby="item-details-tab">
			<div class="row pb-3">
				<div class="items-details-info col-md-12 col-sm-12 col-12 enable-long-words from-wysiwyg">
					
					<div class="row">

						{{-- Price / Salary --}}

						@isset($post['price'])

							<div class="col-md-6 col-sm-6 col-6">
								<h4 class="fw-normal p-0">
									<span class="items-details-info-price">
										{{ \App\Models\Currency::find($post['currency_code'])->symbol}}.{{ number_format($post['price'], 0, '', ',') }}
										{{-- {!! data_get($post, 'price_formatted') !!} --}}
										@if (data_get($post, 'negotiable') == 1)
											<small class="label bg-success"> 
													
												@if(isset($post['price_unit']))
													{{ ucfirst(reverse_slug($post['price_unit'])) }},&nbsp
												@endif

												{{ t('negotiable') }}

											</small>
										@endif
									</span>
								</h4>
							</div>
							
						@endisset

						{{-- Location --}}
						{{-- <div class="col-md-6 col-sm-6 col-6 text-end">
							<h4 class="fw-normal p-0">
								<span class="fw-bold"><i class="bi bi-geo-alt"></i> {{ t('location') }}: </span>
								<span>
									<a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city')) !!}">
										{{ data_get($post, 'city.name') }}
									</a>
								</span>
							</h4>
						</div> --}}
						
					</div>
					
					{{-- Custom Fields --}}
					@includeFirst([config('larapen.core.customizedViewPath') . 'post.show.inc.details.fields-values', 'post.show.inc.details.fields-values'])
			
					{{-- Description --}}
					<div class="row">
												
						<div class="col-12">
							<div class="row mb-2 mt-3">
								<div class="col-12">
									<h4 class="p-0">{{ t('Additional Details') }}</h4>
								</div>
							</div>
						</div>

						<div class="col-12 detail-line-content">
							{!! nl2br($post['description']) !!}
						</div>
					</div>
					
					{{-- Tags --}}
					{{--@if (!empty(data_get($post, 'tags')))
						<div class="row mt-3">
							<div class="col-12">
								<h4 class="p-0 my-3"><i class="bi bi-tags"></i> {{ t('Tags') }}:</h4>
								@foreach(data_get($post, 'tags') as $iTag)
									<span class="d-inline-block border border-inverse bg-light rounded-1 py-1 px-2 my-1 me-1">
										<a href="{{ \App\Helpers\UrlGen::tag($iTag) }}">
											{{ $iTag }}
										</a>
									</span>
								@endforeach
							</div>
						</div>
					@endif--}}
					
					{{-- Actions --}}
					{{-- @if (!auth()->check() || (auth()->check() && auth()->id() != data_get($post, 'user_id')))
						<div class="row text-center h2 mt-4">
							<div class="col-4">
								@if (auth()->check())
									@if (auth()->user()->id == data_get($post, 'user_id'))
										<a href="{{ \App\Helpers\UrlGen::editPost($post) }}">
											<i class="far fa-edit" data-bs-toggle="tooltip" title="{{ t('Edit') }}"></i>
										</a>
									@else
										{!! genEmailContactBtn($post, false, true) !!}
									@endif
								@else
									{!! genEmailContactBtn($post, false, true) !!}
								@endif
							</div>
							@if (isVerifiedPost($post))
								<div class="col-4">
									<a class="make-favorite" id="{{ data_get($post, 'id') }}" href="javascript:void(0)">
										@if (auth()->check())
											@if (!empty(data_get($post, 'savedByLoggedUser')))
												<i class="fas fa-bookmark" data-bs-toggle="tooltip" title="{{ t('Remove favorite') }}"></i>
											@else
												<i class="far fa-bookmark" data-bs-toggle="tooltip" title="{{ t('Save listing') }}"></i>
											@endif
										@else
											<i class="far fa-bookmark" data-bs-toggle="tooltip" title="{{ t('Save listing') }}"></i>
										@endif
									</a>
								</div>
								<div class="col-4">
									<a href="{{ \App\Helpers\UrlGen::reportPost($post) }}">
										<i class="far fa-flag" data-bs-toggle="tooltip" title="{{ t('Report abuse') }}"></i>
									</a>
								</div>
							@endif
						</div>
					@endif --}}
				</div>
			
			</div>
		</div>
		
		@if (config('plugins.reviews.installed'))
			@if (view()->exists('reviews::comments'))
				@include('reviews::comments')
			@endif
		@endif
	</div> 
	
	<div class="d-none d-lg-block">
		<div class="content-footer text-start">
			<div class="md:inline-flex">
				{{--@if (auth()->check())
					@if (auth()->user()->id == data_get($post, 'user_id'))
						<a class="btn btn-default" href="{{ \App\Helpers\UrlGen::editPost($post) }}">
							<i class="far fa-edit"></i> {{ t('Edit') }}
						</a>
					@else
						{!! genPhoneNumberBtn($post) !!}
						{!! genEmailContactBtn($post) !!}
					@endif
				@else
					{!! genPhoneNumberBtn($post) !!}
					{!! genEmailContactBtn($post) !!}
				@endif--}}

				<a href="{{ route('post-ad.edit', ['post' => $post['id'] ]) }}" class="btn btn-default btn-block md:mb-0 mb-2">
					<i class="far fa-edit"></i>&nbsp;Edit ad
				</a>

				@if (config('settings.single.publication_form_type') == '1')
					{{--<a href="{{ url('posts/' . data_get($post, 'id') . '/photos') }}" class="btn btn-default btn-block md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fas fa-camera"></i> {{ t('Update Photos') }}
					</a>--}}
					@if ($countPackages > 0 && $countPaymentMethods > 0)
						<a href="{{  route('post-ad.promote' , ['postId' => data_get($post, 'id')]) }}" class="btn btn-success btn-block md:mb-0 mb-2" style="margin-top:0px;">
							<i class="far fa-check-circle"></i> {{ t('Make It Premium') }}
						</a>
					@endif
				@endif

				{{--@if (empty(data_get($post, 'archived_at')) && isVerifiedPost($post))
					<a href="{{ url('account/posts/list/' . data_get($post, 'id') . '/offline') }}" class="btn btn-warning btn-block confirm-simple-action md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fas fa-eye-slash"></i> {{ t('put_it_offline') }}
					</a>
				@endif--}}

				@if (!empty(data_get($post, 'archived_at')))
					<a href="{{ url('account/posts/archived/' . data_get($post, 'id') . '/repost') }}" class="btn btn-info btn-block confirm-simple-action md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fa fa-recycle"></i> {{ t('re_post_it') }}
					</a>
				@endif
				<div data-modal-target="reportAdModal" data-modal-toggle="reportAdModal" class="btn btn-default btn-block md:mb-0 mb-2" style="margin-top:0px;">
					<i class="fa fa-exclamation"></i>&nbsp;Report ad
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Report Ad Modal modal -->
<div id="reportAdModal" tabindex="-1" aria-hidden="true" class="fixed hidden top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
	<livewire:report-ads postId="{{ data_get($post, 'id') }}" />
</div>

@section('after_styles')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

	<style>
		code {
			color: inherit;
		}

		.swiper-slide:hover{
			cursor: pointer;
		}
	</style>
@endsection

@section('after_scripts')

	<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
	<script>
		
	</script>
@endsection