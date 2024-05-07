<?php
$sectionOptions = $getLocationsOp ?? [];
$sectionData ??= [];
$cities = (array)data_get($sectionData, 'cities');

// Get Admin Map's values
$locCanBeShown = (data_get($sectionOptions, 'show_cities') == '1');
$locColumns = (int)(data_get($sectionOptions, 'items_cols') ?? 3);
$locCountListingsPerCity = (config('settings.list.count_cities_listings'));
$mapCanBeShown = (
	file_exists(config('larapen.core.maps.path') . config('country.icode') . '.svg')
	&& data_get($sectionOptions, 'show_map') == '1'
);

$showListingBtn = (data_get($sectionOptions, 'show_listing_btn') == '1');

$hideOnMobile = (data_get($sectionOptions, 'hide_on_mobile') == '1') ? ' hidden-sm' : '';
?>
@if ($locCanBeShown || $mapCanBeShown)
{{-- @includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.spacer', 'home.inc.spacer'], ['hideOnMobile' => $hideOnMobile]) --}}
<div class="container{{ $hideOnMobile }}">
	<div class="col-xl-12 page-content p-0">
		<div class="inner-box" style="border: 0px;border-radius: 0px;background-color: white;border-top: 1px;border-top-style: solid;border-top-color: #e2e2e2;padding: 0px 30px 30px 30px !important;">

			<div class="row">
				@if (!$mapCanBeShown)
					<div class="row">
						<div class="col-xl-12 col-sm-12">
							<h2 class="title-3 pt-1 pb-3 px-3" style="white-space: nowrap;">
								<i class="fas fa-map-marker-alt"></i>&nbsp;{{ t('Choose a city') }}
							</h2>
						</div>
					</div>
				@endif
				
			</div>

			<?php
				$leftClassCol = '';
				$rightClassCol = '';
				$ulCol = 'col-md-3 col-sm-12'; // Cities Columns
				
				if ($locCanBeShown && $mapCanBeShown) {
					// Display the Cities & the Map
					$leftClassCol = 'col-lg-8 col-md-12';
					$rightClassCol = 'col-lg-3 col-md-12 mt-3 mt-xl-0 mt-lg-0';
					$ulCol = 'col-md-4 col-sm-6 col-12';
					
					if ($locColumns == 2) {
						$leftClassCol = 'col-md-6 col-sm-12';
						$rightClassCol = 'col-md-5 col-sm-12';
						$ulCol = 'col-md-6 col-sm-12';
					}
					if ($locColumns == 1) {
						$leftClassCol = 'col-md-3 col-sm-12';
						$rightClassCol = 'col-md-8 col-sm-12';
						$ulCol = 'col-xl-12';
					}
				} else {
					if ($locCanBeShown && !$mapCanBeShown) {
						// Display the Cities & Hide the Map
						$leftClassCol = 'col-xl-12';
					}
					if (!$locCanBeShown && $mapCanBeShown) {
						// Display the Map & Hide the Cities
						$rightClassCol = 'col-xl-12';
					}
				}
			?>

			<div class="row">
				<div class="col-12 col-md-6 col-lg-5" style="display: flex; align-items: center;">
						@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.locations.svgmap', 'home.inc.locations.svgmap'])
					</div>
				<div class="row col-12 col-md-6 col-lg-7">
					<div class="col-12">
						<span class="fw-bold">Districts</span>
					</div>
					<hr />
							
					@if ($locCanBeShown)
						<div class="col-12 page-content m-0 p-0">
							@if (!empty($cities))
								<div class="relative location-content">
									
									<div class="col-xl-12">
										<div class="row">

											@forelse($alldis as $key => $value)
												<div class="cat-list col-4 mb-0 {{ (count($cities) == $key+1) ? 'cat-list-border' : '' }}">
													<a href="{{ route('search') }}/?l={{ base64_encode(json_encode(['d' => $value['id'] ])) }}">
														{!! str_replace("District", "", $value['name']) !!}
													</a>
												</div>
											@empty
												<div class="col-12">No available districts</div>
											@endforelse

										</div>
									</div>
			
								</div>
							@endif
						</div>
					@endif

				</div>
			</div>
			
		</div>

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
							@if ($showListingBtn)
								@if (!auth()->check() && config('settings.single.guests_can_post_listings') != '1')
									<a class="btn btn-lg btn-listing" href="#quickLogin" data-bs-toggle="modal">
										<i class="far fa-edit"></i> {{ t('Create Listing') }}
									</a>
								@else
									<a class="btn btn-lg btn-listing ps-4 pe-4" href="{{ route('post-ad.index') }}" style="text-transform: none;">
										<i class="far fa-edit"></i> {{ t('Create Listing') }}
									</a>
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
@endif

@section('modal_location')
	@parent
	@if ($locCanBeShown || $mapCanBeShown)
		@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.modal.location', 'layouts.inc.modal.location'])
	@endif
@endsection
