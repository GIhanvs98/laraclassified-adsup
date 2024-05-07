<!-- this (.mobile-filter-sidebar) part will be position fixed in mobile version -->
<div class="col-md-3 page-sidebar mobile-filter-sidebar pb-2 bg-white" style="border-right: solid 1px var(--border-color);">
	<aside>
		<div class="sidebar-modern-inner enable-long-words">
			
			@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.fields', 'search.inc.sidebar.fields'])
			@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.categories', 'search.inc.sidebar.categories'])
            @includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.cities', 'search.inc.sidebar.cities'])
			@if (!config('settings.list.hide_dates'))
				@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.date', 'search.inc.sidebar.date'])
			@endif
			@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.price', 'search.inc.sidebar.price'])
			
		</div>
	</aside>
</div>

@section('after_scripts')
    @parent
    <script>
        var baseUrl = '{{ request()->url() }}';
    </script>
@endsection
