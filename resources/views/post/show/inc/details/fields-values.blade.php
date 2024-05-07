@php
	$customFields ??= [];
@endphp
@if (!empty($customFields))
	<div class="row mt-3">
		<div class="col-12">
			<div class="row gx-1 gy-1">

				@foreach($customFields as $field)

					@php
						$fieldId = data_get($field, 'id');
						$fieldType = data_get($field, 'type');
						$fieldName = data_get($field, 'name');
						$fieldValue = data_get($field, 'value');
					@endphp
					
					@switch($fieldType)

						@case('text')
							@include('post.show.inc.details.fields.input')
						@break

						@case('url')
							@include('post.show.inc.details.fields.input')
						@break

						@case('number')
							@include('post.show.inc.details.fields.input')
						@break

						@case('date')
							@include('post.show.inc.details.fields.input')
						@break

						@case('date_range')
							@include('post.show.inc.details.fields.input')
						@break

						@case('select')
							@include('post.show.inc.details.fields.select')
						@break

						@case('video')
							@include('post.show.inc.details.fields.video')
						@break

						@case('radio')
							@include('post.show.inc.details.fields.input')
						@break

						@case('checkbox')
							@include('post.show.inc.details.fields.checkbox')
						@break

						@case('checkbox_multiple')
							@include('post.show.inc.details.fields.checkbox')
						@break

						@default
							@include('post.show.inc.details.fields.input')

					@endswitch
				
				@endforeach

			</div>
		</div>
	</div>
@endif
