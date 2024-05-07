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


$categories = \App\Models\Category::whereNull('parent_id')->get();

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

			<div class="row">
				<div class="col-12 col-md-6 col-lg-5" style="display: flex; align-items: center;">
					@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.locations.svgmap', 'home.inc.locations.svgmap'])
				</div>
				
                <div class="col-12 col-md-6 col-lg-7">
                    <div class="categories-right mt-4">

						@if (isset($categories))
							<h4>Browse our ad categories</h4>
							<div class="categories-item-wrap">

								@foreach($categories as $key => $category)
									<div class="categories-item">
										<a href="{{ route('search') }}/?c={{ base64_encode(json_encode(['id' => $category->id ])) }}">
											<span class="categories-img">
												<img src="{{ asset($category->picture_url) }}" alt="{{ $category->name }}" style="max-width: 43px !important;">
											</span>
											<p>{{ $category->name }}</p>
											@if (config('settings.list.count_categories_listings'))
												<span>({{ $category->postsCount() }})</span>
											@endif
										</a>
									</div>
								@endforeach

							</div>
						@endif

						@if($locCanBeShown && isset($alldis))
							<div class="districts-title">
								<h4>Districts</h4>
							</div>
							<div class="districts-main">

								@php
			
									$collection = collect($alldis);

									$totalElements = $collection->count();
									$chunks = ceil($totalElements / 5); // Calculate the number of chunks needed

									$chunks = $collection->chunk($chunks);

									$allLocations = $chunks->all();

								@endphp

								@forelse($allLocations as $key => $locationGroup)
									<div class="districts-item">
										<ul>
											@forelse($locationGroup as $key => $location)
											<li>
												<a href="{{ route('search') }}/?l={{ base64_encode(json_encode(['d' => $location['id'] ])) }}">
													{!! str_replace("District", "", $location['name']) !!}
												</a>
											</li>
											@empty

											@endforelse
										</ul>
									</div>
								@empty

								@endforelse

							</div>
						@endif

                    </div>
                </div>
			</div>
			
		</div>
	</div>
</div>
@endif


<div class="container">
	<div class="post-main">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="post-left">
					<div class="post-left-cnt">
						<h3>Post Free Ad Sri Lanka<br> Just in Two Minutes</h3>
						<ul>
							<li>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-4">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								Free Ads Across Categories
							</li>
							<li>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-4">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								Rapid Ad Publication within 4 Hours
							</li>
							<li>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-4">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								Enhanced with 8 Photos per Ad
							</li>
							<li>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-4">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								Promote Ads with Top-Up | Bump-Up
							</li>
							<li>
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-4">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								Manual Review Prevents Spam Ads
							</li>
						</ul>
						<a href="{{ route('post-ad.index') }}">Post Your Ad <span>+</span></a>
					</div>   
				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<div class="d-flex align-items-center h-100">
					<div class="d-block">
						<div class="post-right">
							<div class="post-inner-left">
								<img src="{{ asset('images/mobil4.png') }}" alt="modile app">
							</div>
							<div class="post-inner-right">
								<h3>get your app today</h3>
								<div><a href="#"><img src="images/pay2.svg" alt=""></a></div>
								<div><a href="#"><img src="images/app2.svg" alt=""></a></div>
							</div>
						</div>
						<div class="post-right-btm">
							<h4>Upgrade Your Life! - Buy, Sell, and Succeed with Adsup App</h4>
							<p>Imagine Adsup’s convenience at your fingertips! With Adsup, you can buy, sell, and search with ease. Upgrade your vehicles, sell items both used and new, or find a house, land, jobs, or a mobile phone. Turn your unused items into cash. If you have a service to offer, Adsup makes it easy. Download your app Now</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@php
		$keywordGroups = \App\Models\SearchKeyword::withWhereHas('category')->orderBy('id', 'asc')->orderBy('created_at', 'desc')->limit(4)->get();
	@endphp

	@if($keywordGroups)
		<div class="quick-main">
			<h4>MOST SEARCHED</h4>
			<div class="row">
				@foreach($keywordGroups as $key => $keywordGroup)

					@php
						$keywords = explode(",", $keywordGroup->keywords ?? "");
						
						$filteredKeywords = array_filter($keywords, function($item) {
							return !empty(trim($item));
						});
					@endphp

					@if(isset($filteredKeywords) && count($filteredKeywords) > 0)
							
						<div class="col-lg-3 col-md-6">
							<div class="quick-item line-clamp-20 h-100">
								<h4>{{ $keywordGroup->category->name }}</h4>
								<div style="text-align: center;">
									@foreach($filteredKeywords as $key => $filteredKeyword)
										<a href="{{ route('search', ['q' => $filteredKeyword, 'c' => base64_encode(json_encode(['id' => $keywordGroup->category->id])) ]) }}" style="color: #ebebeb; margin: 4px 5px;">{{ $filteredKeyword }}</a>&nbsp;
									@endforeach
								</div>
							</div>
						</div>

					@endif

				@endforeach
			</div>
		</div>
	@endif

	<div class="about-main">
		<div class="wrapper">
			<div class="text-box">
				<h4>About Adsup, Sri Lanka’s Fastest Growing Free Ad Marketplace</h4>
				<p>Looking for a place to buy, sell, or rent anything in Sri Lanka? Look no further! Adsup is the perfect spot for all your trading needs. We've got 15 main categories and over 60 subcategories, so finding the perfect item is a piece of cake.</p>
				<p>The best part? You can post ads for free – forever! Whether you’re looking to buy, sell, or rent, our user-friendly platform has got you covered. You can buy or sell a house or land, find a car, purchase new or used mobile phones or any electronic items, post job listings, and more.
						</p>
				<h4>Why Choose Adsup? Buy, Sell, Rent Almost Anything Through Adsup</h4>
				<p>Adsup is a rapidly expanding platform for free classifieds, attracting hundreds of new users each month. Whether you’re an individual or a small business owner seeking better advertising options, Adsup is your ideal destination. If you’re looking to buy or sell items such as smartphones, land, houses, electronics, or offer any service, Adsup is your go-to destination.</p>
				<p>Our platform is designed with unbeatable convenience in mind. The user-friendly interface allows for easy searching and filtering, ensuring a seamless experience as you navigate through our diverse listings. From phones and pets to cars and job opportunities, we cater to a wide range of needs. We even foster a spirit of community support with our unique “give away” option.</p>
				<p>For sellers, Adsup offers a hassle-free sign-up. With a lifetime free account that can be created in less than 3 minutes, showcasing your items has never been easier. And while our core service remains free, we also offer a business membership with exclusive privileges. This includes a personalized shop page on our site and special support to make your ads stand out, making it ideal for SMEs and small businesses with multiple items to sell.</p>
				<p>To further boost your visibility, Adsup’s Ad Network presents two paid ad promotion options. These opportunities contribute to the constant growth of our platform, making Adsup not just a marketplace, but a community. Join us today and experience the Adsup difference… </p>
				<h4>Adsup’s Role in Revolutionizing Online Shopping in Sri Lanka</h4>
				<p>In a landscape brimming with classified ad sites in Sri Lanka, many lose their free status and become unaffordable as they gain popularity. It’s often said that a completely free service can’t uphold quality. However, Adsup defies this notion. We pledge to provide a lifetime of free advertising in Sri Lanka, ensuring service quality isn’t compromised. Sounds unbelievable, doesn’t it? But that’s the Adsup promise.</p>
				<h4>The People's Advertising Network: Adsup's User-Friendly Concept</h4>
				<p>Adsup operates on the principle that good-quality advertising doesn’t have to come with a high price tag. We strive to be the people’s advertising network. We continuously update our site and apps, providing a range of features and options that make ad posting a breeze.</p>
				<p>For instance, you can advertise almost any item for sale or rent in just three minutes using our mobile apps or desktop platform, without any category restrictions. Our user-friendly platform is equipped with all the standard features you need, making the advertising process effortless for you..</p>
			</div>
				<div class="toggle_btn">
					<span class="toggle_text">Show More</span> <span class="arrow">
					<i class="fas fa-angle-down"></i>
					</span>
				</div>
			</div>
	</div>
</div>

@section('modal_location')
	@parent
	@if ($locCanBeShown || $mapCanBeShown)
		@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.modal.location', 'layouts.inc.modal.location'])
	@endif
@endsection
