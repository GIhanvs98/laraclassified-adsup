
@php
	
	$pictures = App\Models\Post::find(data_get($post, 'id'))->pictures;

@endphp

{{-- Swiper - Horizontal Thumbnails --}}
<div class="gallery-container">
	@if (!empty($price))
	<!--div class="p-price-tag">{!! $price !!}</div-->
	@endif
	<div class="swiper main-gallery">
		<div class="swiper-wrapper swiper-wrapper-images-container" style="height: inherit !important;">
			@forelse($pictures as $key => $image)
				<div class="swiper-slide swiper-slide-images-container" style="height: inherit !important;">
					{!! imgTag(data_get($image, 'filename'), 'big', ['alt' => $titleSlug . '-big-' . $key]) !!}
				</div>
			@empty
				<div class="swiper-slide swiper-wrapper-images-container" style="height: inherit !important;">
					<img src="{{ imgUrl(config('larapen.core.picture.default'), 'big') }}" alt="img" class="default-picture" style="height: inherit !important;">
				</div>
			@endforelse
		</div>
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
	</div>
	<div class="swiper thumbs-gallery">
		<div class="swiper-wrapper">
			@forelse($pictures as $key => $image)
				<div class="swiper-slide">
					{!! imgTag($image->thumbnailImage->filename, 'small', ['alt' => $titleSlug . '-small-' . $key]) !!}
				</div>
			@empty
				<div class="swiper-slide">
					<img src="{{ imgUrl(config('larapen.core.picture.default'), 'small') }}" alt="img" class="default-picture">
				</div>
			@endforelse
		</div>
	</div>
</div>

{{-- Jquery --}}
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

<script>
	$(document).ready(swiperWrapperResize);

	$(document).on('resize', swiperWrapperResize);

	$(window).on('resize', swiperWrapperResize);

	function swiperWrapperResize() {

		$(".swiper-wrapper .swiper-slide-images-container").each(function(index, element) {

			let containerWidth = $(element).width();
			let containerHeight = $(element).height();

			let r = containerWidth / containerHeight;

			let image = $(element).find("img");

			let imageWidth = image.width();
			let imageHeight = image.height();

			let r_image = imageWidth / imageHeight;

			// console.log("R =" + r + " r_image=" + r_image + ";");

			if (r > r_image) {

				// Lower width than given ratio, or higher height than given ratio.

				// Fixed height and image width will be streched to adjust to that. ( Potrait images )

				// console.log("Fixed height and image height will be streched to adjust to that.");

				image.addClass("potrait-images");

			} else if (r == r_image) {

				// All are in perfect.

				// 100% width and height.

				// console.log("100% width and height.");

				image.addClass("landscape-images");

			} else {

				// R < r_image 

				// Higher width than given ratio, or lower height than given ratio.

				// Fixed width and image width will be streched to adjust to that. ( Landscape images )

				// console.log("Fixed width and image width will be streched to adjust to that.");

				image.addClass("landscape-images");

			}

		});
	}

</script>

@section('after_styles')
	@parent
	<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-bundle.min.css') }}" rel="stylesheet"/>
	<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-horizontal-thumbs.css') }}" rel="stylesheet"/>
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/swiper/7.4.1/swiper-horizontal-thumbs-rtl.css') }}" rel="stylesheet"/>
	@endif
@endsection
@section('after_scripts')
	@parent
	<script src="{{ url('assets/plugins/swiper/7.4.1/swiper-bundle.min.js') }}"></script>
	<script>
		$(document).ready(function () {
			var thumbsGalleryOptions = {
				slidesPerView: 2,
				spaceBetween: 5,
				freeMode: true,
				watchSlidesProgress: true,
				/* Responsive breakpoints */
				breakpoints: {
					/* when window width is >= 320px */
					320: {
						slidesPerView: 3
					},
					/* when window width is >= 576px */
					576: {
						slidesPerView: 4
					},
					/* when window width is >= 768px */
					768: {
						slidesPerView: 5
					},
					/* when window width is >= 992px */
					992: {
						slidesPerView: 6
					},
				},
				centerInsufficientSlides: true,
				direction: 'horizontal',
			};
			var thumbsGallery = new Swiper('.thumbs-gallery', thumbsGalleryOptions);
			
			var mainGalleryOptions = {
				speed: 300,
				loop: true,
				spaceBetween: 10,
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				thumbs: {
					swiper: thumbsGallery,
				},
				autoHeight: true,
				grabCursor: true,
			};
			var mainGallery = new Swiper('.main-gallery', mainGalleryOptions);
			
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