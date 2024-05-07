{{-- Swiper - Vertical Thumbnails --}}
<div class="gallery-container">
	<div class="swiper-container main-gallery">
		<div class="swiper-wrapper">
			@forelse($pictures as $key => $image)
				@if (!empty($price))
					<div class="p-price-tag">{!! $price !!}</div>
				@endif
				<div class="swiper-slide">
					{!! imgTag(data_get($image, 'filename'), 'big', ['alt' => $titleSlug . '-big-' . $key]) !!}
				</div>
			@empty
				@if (!empty($price))
					<div class="p-price-tag">{!! $price !!}</div>
				@endif
				<div class="swiper-slide">
					<img src="{{ imgUrl(config('larapen.core.picture.default'), 'big') }}" alt="img" class="default-picture">
				</div>
			@endforelse
			
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
	</div>
	<div class="swiper-container thumbs-gallery">
		<div class="swiper-wrapper">
			@forelse($pictures as $key => $image)
				<div class="swiper-slide">
					{!! imgTag(data_get($image, 'filename'), 'small', ['alt' => $titleSlug . '-small-' . $key]) !!}
				</div>
			@empty
				<div class="swiper-slide">
					<img src="{{ imgUrl(config('larapen.core.picture.default'), 'small') }}" alt="img" class="default-picture">
				</div>
			@endforelse
		</div>
	</div>
</div>

@section('after_styles')
	@parent
	<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-bundle.min.css') }}" rel="stylesheet"/>
	<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-vertical-thumbs.css') }}" rel="stylesheet"/>
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-vertical-thumbs-rtl.css') }}" rel="stylesheet"/>
	@endif
@endsection
@section('after_scripts')
	@parent
	<script src="{{ url('assets/plugins/swiper/7.4.1/swiper-bundle.min.js') }}"></script>
	<script>
		var totalSlides = {{ count((array)$pictures) }};
		$(document).ready(function () {
			var thumbsGalleryOptions = {
				slidesPerView: 4,
				spaceBetween: 10,
				direction: 'vertical',
				slideToClickedSlide: true,
				loopedSlides: 50,
				loop: (totalSlides >= 4),
			};
			var thumbsGallery = new Swiper('.thumbs-gallery', thumbsGalleryOptions);
			
			var mainGalleryOptions = {
				speed: 300,
				slidesPerView: 1,
				loop: true,
				loopedSlides: 50,
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				effect: 'fade',
				fadeEffect: {crossFade: true},
				thumbs: {swiper: thumbsGallery},
				lazy: true,
			};
			var mainGallery = new Swiper('.main-gallery', mainGalleryOptions);
			
			$('.thumbs-gallery').on('click', '.swiper-slide', function() {
				mainGallery.slideTo($(this).index(), 500);
			});
			
			mainGallery.on('click', function (swiper, event) {
				/* console.log(swiper); */
				if (typeof swiper.clickedSlide === 'undefined') {
					return false;
				}
				
				var imgEl = swiper.clickedSlide.querySelector('img');
				if (typeof imgEl === 'undefined' || typeof imgEl.src === 'undefined') {
					return false;
				}
				
				var currentSrc = imgEl.src;
				var imgTitle = "{{ data_get($post, 'title') }}";
				
				var wrapperSelector = '.main-gallery .swiper-slide:not(.swiper-slide-duplicate) img:not(.default-picture)';
				var imgSrcArray = getFullSizeSrcOfAllImg(wrapperSelector, currentSrc);
				if (imgSrcArray === undefined || imgSrcArray.length == 0) {
					return false;
				}
				
				{{-- Load full size pictures slides dynamically --}}
				var swipeboxItems = formatImgSrcArrayForSwipebox(imgSrcArray, imgTitle);
				var swipeboxOptions = {
					hideBarsDelay: (1000 * 60 * 5),
					loopAtEnd: false
				};
				$.swipebox(swipeboxItems, swipeboxOptions);
			});
		});
	</script>
@endsection