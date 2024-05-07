@if (socialLoginIsEnabled())

	@if (config('settings.social_auth.facebook_client_id') && config('settings.social_auth.facebook_client_secret'))
		<a href="{{ url('auth/facebook') }}" class="btn btn-fb btn-block social-login-bt">
			<i class="fab fa-facebook"></i> {!! t('Login with Facebook') !!}
		</a>
	@endif

	@if (config('settings.social_auth.linkedin_client_id') && config('settings.social_auth.linkedin_client_secret'))
		<a href="{{ url('auth/linkedin') }}" class="btn btn-lkin btn-block social-login-bt">
			<i class="fab fa-linkedin"></i> {!! t('Login with LinkedIn') !!}
		</a>
	@endif

	@if (config('settings.social_auth.twitter_client_id') && config('settings.social_auth.twitter_client_secret'))
		<a href="{{ url('auth/twitter') }}" class="btn btn-tw btn-block social-login-bt">
			<i class="fab fa-twitter"></i> {!! t('Login with Twitter') !!}
		</a>
	@endif
	
	@if (config('settings.social_auth.google_client_id') && config('settings.social_auth.google_client_secret'))
		<a href="{{ url('auth/google') }}" class="btn btn-ggl btn-block social-login-bt">
			<i class="fab fa-google"></i> {!! t('Login with Google') !!}
		</a>
	@endif

@endif