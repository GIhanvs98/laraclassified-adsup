@if (config('services.googlemaps.key'))
	<?php
	$mapHeight = 400;
	$mapPlace = (isset($city) && !empty($city))
		? $city->name . ', ' . config('country.name')
		: config('country.name');
	$mapUrl = getGoogleMapsEmbedUrl(config('services.googlemaps.key'), $mapPlace);
	?>
	<div class="intro-inner" style="height: {{ $mapHeight }}px;">
		<iframe
				id="googleMaps"
				width="100%"
				height="{{ $mapHeight }}"
				style="border:0;"
				loading="lazy"
				title="{{ $mapPlace }}"
				aria-label="{{ $mapPlace }}"
				src="{{ $mapUrl }}"
		></iframe>
	</div>
@endif

@section('after_scripts')
	@parent
	@if (config('services.googlemaps.key'))
	<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
	<script>
		$(document).ready(function () {
			{{--
			let mapUrl = '{{ addslashes($mapUrl) }}';
			/* console.log(mapUrl); */
			
			let iframe = document.getElementById('googleMaps');
			iframe.setAttribute('src', mapUrl);
			--}}
		});
	</script>
	@endif
@endsection