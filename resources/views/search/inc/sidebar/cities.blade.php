<?php
// Clear Filter Button
$clearFilterBtn = \App\Helpers\UrlGen::getCityFilterClearLink($cat ?? null, $city ?? null);
?>
<?php
/*
 * Check if the City Model exists in the Cities eloquent collection
 * If it doesn't exist in the collection,
 * Then, add it into the Cities eloquent collection
 */
if (isset($cities, $city) && !collect($cities)->contains($city)) {
	collect($cities)->push($city)->toArray();
}
?>
{{-- City --}}
<div class="block-title has-arrow sidebar-header">
	<h5>
		<span class="fw-bold">
			Cities
		</span> {!! $clearFilterBtn !!}
	</h5>
</div>
<div class="block-content list-filter locations-list">

	<div class="slidebar">
	@php
	
	$routeText = 'ads';
	
	@endphp

	<div><a href="{{ route('search') }}" style="font-size: 14px;color: #4e575d;">@if(isset($districtId))All of Sri Lanka @else<strong>All of Sri Lanka</strong>@endif</a></div>

	<ul style="margin: 0px;" class="browse-list list-unstyled long-list">

		@if(isset($districtId))
			
			{{-- Output for the http://127.0.0.1:8000/ads/{city}/{id} --}}

			@if(DB::table('subadmin2')->where('district_id_city', $districtId)->where('active', 1)->exists())
				
				{{-- Is a district --}}
				
				@php
					
				$district = DB::table('cities')->whereId($districtId)->where('active', 1)->first();

				$cities = DB::table('cities')->whereNotIn('id', [$districtId])->where('active', 1)->where('subadmin2_code', $district->subadmin2_code)->get();

				@endphp

				<li>
					
					<div class="district-container">
						<strong><a href="{{ route($routeText, ['city' => str()->slug(json_decode($district->name)->en), 'id' => $district->id]) }}">
							{{ json_decode($district->name)->en }}
						</a></strong>
					</div>
					<ul>
						@forelse($cities as $key => $city)
							
							{{-- Calculate the total posts --}}
							@php

							$postsCount = DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
							
							@endphp

							<li class="district-container">
								<a href="{{ route($routeText, ['city' => str()->slug(json_decode($city->name)->en), 'id' => $city->id]) }}">
									{{ json_decode($city->name)->en }}&nbsp;<span class="count">({{ $postsCount }})</span>
								</a>
							</li>
						@empty
							
							<div class="city">No cities found</div>
						@endforelse
					</ul>
				</li>

			@else
				
				{{-- Not a district --}}

				@php
					
				$districtCode = DB::table('cities')->whereId($districtId)->where('active', 1)->first()->subadmin2_code;

				$district = DB::table('subadmin2')->where('code', $districtCode)->where('active', 1)->first();

				$cities = DB::table('cities')->whereNotIn('id', [$district->district_id_city])->where('active', 1)->where('subadmin2_code', $districtCode)->get();

				@endphp
				
				<li>
					
					<div class="district-container">
						<a href="{{ route($routeText, ['city' => str()->slug(str_replace(' District', '', json_decode($district->name)->en)), 'id' => $district->district_id_city]) }}">
							{{ str_replace(' District', '', json_decode($district->name)->en) }}
						</a>
					</div>
					<ul>
						@forelse($cities as $key => $city)   

							{{-- Calculate the total posts --}}
							@php

							$postsCount = DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
							
							@endphp

							<li class="district-container">
								<a href="{{ route($routeText, ['city' => str()->slug(json_decode($city->name)->en), 'id' => $city->id]) }}">
									@if( $city->id == $districtId )
										<strong>{{ json_decode($city->name)->en }}</strong>
									@else
										{{ json_decode($city->name)->en }}&nbsp;<span class="count">({{ $postsCount }})</span>
									@endif
								</a>
							</li>
						@empty
							
							<div class="city">No cities found</div>
						@endforelse
					</ul>
				</li>

			@endif
			
		@else
				
			@php

			$districts = DB::table('subadmin2')->where('active', 1)->whereNotNull('district_id_city')->orderBy('name', 'asc')->get();
				
			@endphp

			{{-- Output for the http://127.0.0.1:8000/ads/ --}}
			
			@forelse($districts as $key => $district)
				
				{{-- Calculate the total posts --}}
				@php

				$postsCount = 0;

				$cities = DB::table('cities')->where('active', 1)->where('subadmin2_code', $district->code)->get();
				
				foreach ($cities as $key => $city) {

					$postsCount += DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
				}
				
				@endphp

				<li class="district-container">
					<a href="{{ route($routeText, ['city' => str()->slug(str_replace(' District', '', json_decode($district->name)->en)), 'id' => $district->district_id_city]) }}">
						{{ str_replace(' District', '', json_decode($district->name)->en) }}&nbsp;<span class="count">({{ $postsCount }})</span>
					</a>
				</li>
			@empty
				
				<div class="district-container">No districts found</div>
			@endforelse

		@endif
	</ul>
	</div>



	{{-- <ul class="browse-list list-unstyled long-list">
		@if (isset($cities) && !empty($cities))
			@foreach ($cities as $iCity)
				<li>
					@if (
						(
							isset($city)
							&& data_get($city, 'id') == data_get($iCity, 'id')
						)
						|| request()->input('l') == data_get($iCity, 'id')
						)
						<strong>
							<a href="{!! \App\Helpers\UrlGen::city($iCity, null, $cat ?? null) !!}" title="{{ data_get($iCity, 'name') }}">
								{{ data_get($iCity, 'name') }}
								@if (config('settings.list.count_cities_listings'))
									<span class="count">&nbsp;{{ data_get($iCity, 'posts_count') ?? 0 }}</span>
								@endif
							</a>
						</strong>
					@else
						<a href="{!! \App\Helpers\UrlGen::city($iCity, null, $cat ?? null) !!}" title="{{ data_get($iCity, 'name') }}">
							{{ data_get($iCity, 'name') }}
							@if (config('settings.list.count_cities_listings'))
								<span class="count">&nbsp;{{ data_get($iCity, 'posts_count') ?? 0 }}</span>
							@endif
						</a>
					@endif
				</li>
			@endforeach
		@endif
	</ul> --}}

</div>
<div style="clear:both"></div>