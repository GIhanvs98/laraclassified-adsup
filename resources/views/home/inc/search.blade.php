@php
	$sectionOptions = $getSearchFormOp ?? [];
	$sectionData ??= [];
	
	// Get Search Form Options
	$enableFormAreaCustomization = data_get($sectionOptions, 'enable_extended_form_area') ?? '0';
	$hideTitles = data_get($sectionOptions, 'hide_titles') ?? '0';
	
	$headerTitle = data_get($sectionOptions, 'title_' . config('app.locale'));
	$headerTitle = (!empty($headerTitle)) ? replaceGlobalPatterns($headerTitle) : null;
	
	$headerSubTitle = data_get($sectionOptions, 'sub_title_' . config('app.locale'));
	$headerSubTitle = (!empty($headerSubTitle)) ? replaceGlobalPatterns($headerSubTitle) : null;
	
	$parallax = data_get($sectionOptions, 'parallax') ?? '0';
	$hideForm = data_get($sectionOptions, 'hide_form') ?? '0';
	$displayStatesSearchTip = config('settings.list.display_states_search_tip');
	
	$hideOnMobile = (data_get($sectionOptions, 'hide_on_mobile') == '1') ? ' hidden-sm' : '';
@endphp
@if (isset($enableFormAreaCustomization) && $enableFormAreaCustomization == '1')
	
	@if (isset($firstSection) && !$firstSection)
		<div class="p-0 mt-lg-4 mt-md-3 mt-3"></div>
	@endif
	
	@php
		$parallax = ($parallax == '1') ? ' parallax' : '';
	@endphp
		
	<!-- start hero-section -->
	<div class="hero-area">
		<div class="main-content1">
			<div id="owl-csel1" class="owl-carousel owl-theme">
				<div class="slide-item">
					<a href="#">
						<img src="{{ asset('images/slider-2.jpg') }}" alt="">
					</a>
				</div>
				<div class="slide-item">
					<a href="#">
						<img src="{{ asset('images/slider-1.jpg') }}" alt="">
					</a>
				</div>
				<div class="slide-item">
					<a href="#">
						<img src="{{ asset('images/slider-2.jpg') }}" alt="">
					</a>
				</div>
				<div class="slide-item">
					<a href="#">
						<img src="{{ asset('images/slider-1.jpg') }}" alt="">
					</a>
				</div>
			</div>
			<div class="owl-theme">
				<div class="owl-controls">
					<div class="custom-nav owl-nav"></div>
				</div>
			</div>
		</div>
	</div>
	<style>
		.owl-dots {
			display: none !important;
		}
	</style>
	<!-- end hero-section -->

	<!-- start search-section -->
	<div class="intro{{ $hideOnMobile }}{{ $parallax }}">
		<div class="container text-center">
			
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <button class="btn btn-success border border-1 border-light rounded-4 all-of-sri-lanka--bt">
                            <i class="bi bi-geo-alt-fill"></i> 
							<span class="city-name">All of Sri Lanka</span>
                        </button>
                    </div>
                </div>

				<style>
					.all-of-sri-lanka--bt{
						font-weight: 700;
						font-size: 15px;
						color: #ffffff;
						background: #065cad !important;
					}

					.all-of-sri-lanka--bt:hover{
						opacity: 0.9;
					}
				</style>

                <!--div class="row justify-content-center">
                    <div class="col-9 col-md-7 col-lg-5 mt-3 mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control px-4 py-2 rounded-start-5 border-0"
                                placeholder="What are you looking for?" aria-label="What are you looking for?"
                                aria-describedby="basic-addon1">
                            <button class="btn bg-white border-0 rounded-end-5 pe-4"><i
                                    class="text-warning bi bi-search"></i></button>
                        </div>
                    </div>
                </div-->

			@if ($hideTitles != '1')
				<!--h1 class="intro-title animated fadeInDown">
					{{ $headerTitle }}
				</h1-->
				<!--p class="sub animateme fittext3 animated fadeIn">
					{!! $headerSubTitle !!}
				</p-->
			@endif
			
			@if ($hideForm != '1')
					<form id="search" name="search" action="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" method="GET">
						<div class="d-flex search-row animated fadeInUp" style="background: white;">
							
							<div class="search-col relative mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0 w-100" style="margin: 0px !important;">
								<div class="search-col-inner">
									<!--i class="fas {{ (config('lang.direction')=='rtl') ? 'fa-angle-double-left' : 'fa-angle-double-right' }} icon-append"></i-->
									<div class="search-col-input">
										<input class="form-control has-icon search-field" name="q" placeholder="{{ t('what') }}" type="text" value="" style="padding-left: .5rem; padding-right: .5rem;p">
									</div>
								</div>
							</div>
							
							{{--<input type="hidden" id="lSearch" name="l" value="">--}}
							
							<div class="home-location-btn-div mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0 hidden">
								<div class="search-col-inner">
									<i class="fas fa-map-marker-alt icon-append"></i>
									<div class="search-col-input">
										@if ($displayStatesSearchTip)
											<input class="form-control locinput input-rel searchtag-input has-icon" id="locSearch" {{--name="location"--}} name="l" placeholder="{{ t('where') }}" type="text" value="" data-bs-placement="top" data-bs-toggle="tooltipHover" hidden title="{{ t('Enter a city name OR a state name with the prefix', ['prefix' => t('area')]) . t('State Name') }}">
										@else
											<input class="form-control locinput input-rel searchtag-input has-icon" id="locSearch" {{--name="location"--}} name="l" placeholder="{{ t('where') }}" type="text" value="" hidden>
										@endif
									</div>
								</div>
								<button type="button" class="btn btn-primary home-location-btn" data-bs-toggle="modal" data-bs-target="#exampleModal">
								      Location
								</button>
							</div>
							
							<div class="search-col">
								<div class="search-btn-border bg-primary bt-search-container" style="padding-left: .5rem;padding-right: 0.2rem;">
									<button class="btn btn-primary btn-search btn-block btn-gradient" style="border-radius: 90px !important; padding: 0px;">
										<!--i class="text-warning fas fa-search" style="text-shadow: none;width: 45px;display: flex;"></i--> <strong>Search</strong>
									</button>
								</div>
							</div>
							
						</div>
					</form>
								
					<div class="categories-title text-center">
						<h4 style="line-height: normal;">Experience Sri Lanka’s Largest Lifetime Free Classified Ads Marketplace</h4>
						<p style="letter-spacing: 0.1px;">Welcome to Adsup.lk! Revolutionize your online market experience in Sri Lanka. Buy, sell, or search for cars, phones, property, jobs, and more. Enjoy lifetime free ads!</p>
					</div>
			@endif
			
		</div>
	</div>
	<!-- end search-section -->

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="border: 0px;">
				<h5 class="modal-title" id="exampleModalLabel" style="display: none;">Select City</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 10px;padding: 10px;"></button>
			</div>
			<div class="modal-body" style="padding:10px 40px 40px 40px;">
				<div class="row">
					<div class="col">
					
						<ul class="nav flex-column" style="border-bottom: 1px solid #d4ded9;">
						<li><h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px;">Districts</h5></li>
					
							<li class="nav-item serp-locations parent-locations disc" location-value="" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
								<button class="nav-link button-list active" aria-current="page" href="#" style="padding-left: 0px;">All of Sri Lanka</button>
							</li>

							@if(isset($alldis))
								@foreach ($alldis as $alldi)
									<li class="nav-item serp-locations parent-locations disc" location-value="{{$alldi['originalID']}}" style="border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;">
										<button class="nav-link button-list active " aria-current="page" href="#" style="padding-left: 0px;">{{ str_replace(" District","", $alldi['name']) }}</button>
									</li>
								@endforeach
							@endif
						</ul>
					</div>
					<div class="col" id="subLocationContainer-model">
						<li><h5 class="modal-title" style="padding: 0.5rem 1rem 0.5rem 0px;">Cities</h5></li>
						<ul class="nav flex-column" id="bbbbbbbbb" style="border-bottom: 1px solid #d4ded9;">
							
						<ul>
					</div>
				</div>
				
			</div>
			</div>
		</div>
	</div>

	
@else
	
	@includeFirst([config('larapen.core.customizedViewPath') . 'home.inc.spacer', 'home.inc.spacer'])
	<div class="intro only-search-bar{{ $hideOnMobile }}">
		<div class="container text-center">
			
			@if ($hideForm != '1')
				<form id="search" name="search" action="{{ \App\Helpers\UrlGen::searchWithoutQuery() }}" method="GET">
					<div class="row search-row animated fadeInUp" style="background: white;">
						
						<div class="col-md-5 col-sm-12 search-col relative mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0">
							<div class="search-col-inner">
								<i class="fas {{ (config('lang.direction')=='rtl') ? 'fa-angle-double-left' : 'fa-angle-double-right' }} icon-append"></i>
								<div class="search-col-input">
									<input class="form-control has-icon" name="q" placeholder="{{ t('what') }}" type="text" value="">
								</div>
							</div>
						</div>
						
						{{--<input type="hidden" id="lSearch" name="l" value="">--}}
						
						<div class="col-md-5 col-sm-12 search-col relative locationicon mb-1 mb-xxl-0 mb-xl-0 mb-lg-0 mb-md-0">
							<div class="search-col-inner">
								<i class="fas fa-map-marker-alt icon-append"></i>
								<div class="search-col-input">
									@if ($displayStatesSearchTip)
										<input class="form-control locinput input-rel searchtag-input has-icon"
											   id="locSearch"
												{{--name="location"--}}
												name="l"
											   placeholder="{{ t('where') }}"
											   type="text"
											   value=""
											   data-bs-placement="top"
											   data-bs-toggle="tooltipHover"
											   title="{{ t('Enter a city name OR a state name with the prefix', ['prefix' => t('area')]) . t('State Name') }}"
										>
									@else
										<input class="form-control locinput input-rel searchtag-input has-icon"
											   id="locSearch"
												{{--name="location"--}}
												name="l"
											   placeholder="{{ t('where') }}"
											   type="text"
											   value=""
										>
									@endif
								</div>
							</div>
						</div>
						
						<div class="col-md-2 col-sm-12 search-col">
							<div class="search-btn-border bg-primary">
								<button class="btn btn-primary btn-search btn-block btn-gradient">
									<i class="fas fa-search"></i> <strong>{{ t('find') }}</strong>
								</button>
							</div>
						</div>
					
					</div>
				</form>
			@endif
		
		</div>
	</div>
	
@endif

@section('before_scripts')

<script type="text/javascript">
	$(document).ready(function () {
		
		$("#subLocationContainer-model").hide();

		$( ".parent-locations" ).on( "click", function() {

			$( ".parent-locations" ).removeClass("locations-parent-active");

			$(this).addClass("locations-parent-active");

			var code = $(this).attr("location-value");

			if(code == "" || code == null){

				$("#subLocationContainer-model").hide();

			}else{
			
				if( $( "#bbbbbbbbb" ).text() == "" || $( "#bbbbbbbbb" ).text() == null ){

					$("#subLocationContainer-model").hide();
				}else{

					$("#subLocationContainer-model").show();
				}
			}

		});
		
		$(".disc").click(function(){

			const code = $(this).attr("location-value");

			const text = $(this).text();

			if(code == "" || code == null){

				document.getElementById("locSearch").value = code;
				$('#exampleModal').modal('hide');

			}else{

				$.ajax({
					type:'GET',
					url:'ajax/getCitesByDisID/'+code,
					dataType: 'json',
					success:function(data) {
							
							$( "#bbbbbbbbb" ).html("");	

							const locArray = { d: data.disCode };

							let locJsonString = JSON.stringify(locArray);

							const encodedLocID = btoa(locJsonString); // encode a string

							$( "#bbbbbbbbb" ).append( "<li class='nav-item serp-locations testDistrict' location-value="+encodedLocID+" style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;'><button class='nav-link button-list active' value="+text+">All of " + text + "</button></li>" );	
								
							data.data.forEach(function(element){

								var Name = JSON.parse(element.name);

								// category : c={"id":23, "subId":45}

								const categoryArray = { c: element.id };

								let categoryJsonString = JSON.stringify(categoryArray);

								const encodedCategory = btoa(categoryJsonString); // encode a string

								$( "#bbbbbbbbb" ).append( "<li class='nav-item serp-locations testCity' location-value="+encodedCategory+" style='border-top: 1px solid #d4ded9;display: flex;justify-content: space-between;align-items: center;'><button class='nav-link button-list active' value="+Name.en+">" + Name.en + "</button></li>" );	
								clickFirstButton();
							});

							$(".serp-locations button").on("click", function () {
								$(".all-of-sri-lanka--bt span.city-name").text($(this).text());
							});
					}
				});
			}

		});

		function clickFirstButton(value){		   
			$(".testCity").click(function(){
				var value = $(this).attr("location-value");
				document.getElementById("locSearch").value = value;
				$('#exampleModal').modal('hide');
			});   

			$(".testDistrict").click(function(){
				var value = $(this).attr("location-value");
				document.getElementById("locSearch").value = value;
				$('#exampleModal').modal('hide');
				$("#subLocationContainer-model").hide();
			});
		}  
});


$(function () {

	$(".all-of-sri-lanka--bt").on("click", function () {
		$(".home-location-btn").trigger( "click" );
	});

});
</script>

@endsection
