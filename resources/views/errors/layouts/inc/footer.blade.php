<?php
$socialLinksAreEnabled = (
	config('settings.social_link.facebook_page_url')
	|| config('settings.social_link.twitter_url')
	|| config('settings.social_link.tiktok_url')
	|| config('settings.social_link.linkedin_url')
	|| config('settings.social_link.pinterest_url')
	|| config('settings.social_link.instagram_url')
);

$appsLinksAreEnabled = (
	config('settings.other.ios_app_url')
	|| config('settings.other.android_app_url')
);
$socialAndAppsLinksAreEnabled = ($socialLinksAreEnabled || $appsLinksAreEnabled);

?>


     
<!-- footer-section -->
<footer class="footer-area">
    <div class="container">
        <div class="footer-main" style="justify-content: space-between;display: flex;">

			@if ($appsLinksAreEnabled)
				<div class="footer-item1 footer-item">
					<h4>Download our Adsup app</h4>
					<div class="footer1-box">
						<ul>
							<li>
								<a href="{{ route('home') }}"><img src="{{ asset('images/footer-logo.png') }}" alt="{{ strtolower(config('settings.app.name')) }}"></a>
							</li>
							<li>		
								@if (config('settings.other.android_app_url'))			
									<a target="_blank" href="{{ config('settings.other.android_app_url') }}">
										@include('layouts.inc.svgs.play-store')
									</a>
								@endif
								<br>
								@if (config('settings.other.ios_app_url'))		
									<a target="_blank" href="{{ config('settings.other.ios_app_url') }}">
										@include('layouts.inc.svgs.app-store')
									</a>
								@endif
							</li>
						</ul>
					</div>
				</div>
			@endif

			@if (isset($pages) && $pages->count() > 0)
				<div class="footer-item">
					<h4>About Adsup</h4>	
					<ul>
						@foreach($pages as $page)
							<li>
								<?php
									$linkTarget = ($page->target_blank == 1) ? 'target="_blank"' : '' ;
								?>
								@if (!empty($page->external_link))
									<a href="{!! $page->external_link !!}" rel="nofollow" {!! $linkTarget !!}> {{ $page->name }} </a>
								@else
									<a href="{{ \App\Helpers\UrlGen::page($page) }}" {!! $linkTarget !!}> {{ $page->name }} </a>
								@endif
							</li>
						@endforeach
					</ul>
				</div>
			@endif

            <div class="footer-item">
                <h4>{{ t('Contact and Sitemap') }}</h4>
                <ul>
                    <li><a href="{{ \App\Helpers\UrlGen::contact() }}"> {{ t('Contact') }} </a></li>
					<li><a href="{{ \App\Helpers\UrlGen::sitemap() }}"> {{ t('sitemap') }} </a></li>
					@if (isset($countries) && $countries->count() > 1)
						<li><a href="{{ \App\Helpers\UrlGen::countries() }}"> {{ t('countries') }} </a></li>
					@endif
                </ul>
            </div>

            <div class="footer-item">
                <h4>{{ t('My Account') }}</h4>
                <ul>
                   @if (!auth()->user())
						<li>
							@if (config('settings.security.login_open_in_modal'))
								<a href="#quickLogin" data-bs-toggle="modal"> {{ t('log_in') }} </a>
							@else
								<a href="{{ \App\Helpers\UrlGen::login() }}"> {{ t('log_in') }} </a>
							@endif
						</li>
						<li><a href="{{ \App\Helpers\UrlGen::register() }}"> {{ t('register') }} </a></li>
					@else
						<li><a href="{{ url('account') }}"> {{ t('My Account') }} </a></li>
						<li><a href="{{ url('account/posts/list') }}"> {{ t('my_listings') }} </a></li>
						<li><a href="{{ url('account/posts/favourite') }}"> {{ t('favourite_listings') }} </a></li>
					@endif
                </ul>
            </div>

			@if ($socialLinksAreEnabled) 
				<div class="footer-item">
					<h4>Follow Adsup</h4>
					<ul>
						@if (config('settings.social_link.facebook_page_url'))
							<li><a href="{{ config('settings.social_link.facebook_page_url') }}">Facebook</a></li>
						@endif

						@if (config('settings.social_link.twitter_url'))
							<li><a href="{{ config('settings.social_link.twitter_url') }}">Twitter</a></li>
						@endif

						@if (config('settings.social_link.instagram_url'))
							<li><a href="{{ config('settings.social_link.instagram_url') }}">Instagram</a></li>
						@endif

						@if (config('settings.social_link.linkedin_url'))
							<li><a href="{{ config('settings.social_link.linkedin_url') }}">LinkedIn</a></li>
						@endif

						@if (config('settings.social_link.pinterest_url'))
							<li><a href="{{ config('settings.social_link.pinterest_url') }}">Pinterest</a></li>
						@endif

						@if (config('settings.social_link.tiktok_url'))
							<li><a href="{{ config('settings.social_link.tiktok_url') }}" >Tiktok</a></li>
						@endif
					</ul>
				</div>
			@endif

        </div>
    </div>
    <div class="copyright-area">
        <div class="container">
            <div class="copyright-main">
                <div class="copyright-left">
                    <p>Copyright <a href="{{ route('home') }}">RTP Lanka Solutions (Pyt) Ltd</a></p>
                </div>
                <div class="copyright-right">
                    <a href="{{ route('home') }}">
                        <img src="{{ config('settings.app.logo_url') }}" alt="{{ strtolower(config('settings.app.name')) }}">
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>