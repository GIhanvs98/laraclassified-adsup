@if (socialLoginIsEnabled())

	@if (config('settings.social_auth.facebook_client_id') && config('settings.social_auth.facebook_client_secret'))
		<a href="{{ url('auth/facebook') }}" class="btn btn-fb rounded-circle social-login-bt">
			<i class="fab fa-facebook"></i>
		</a>
	@endif

	@if (config('settings.social_auth.linkedin_client_id') && config('settings.social_auth.linkedin_client_secret'))
		<a href="{{ url('auth/linkedin') }}" class="btn btn-lkin rounded-circle social-login-bt">
			<i class="fab fa-linkedin"></i>
		</a>
	@endif

	@if (config('settings.social_auth.twitter_client_id') && config('settings.social_auth.twitter_client_secret'))
		<a href="{{ url('auth/twitter') }}" class="btn btn-tw rounded-circle social-login-bt">
			<i class="fab fa-twitter"></i>
		</a>
	@endif
	
	@if (config('settings.social_auth.google_client_id') && config('settings.social_auth.google_client_secret'))
		<a href="{{ url('auth/google') }}" class="btn btn-ggl rounded-circle social-login-bt">
			<i class="fab fa-google"></i>
		</a>
	@endif

@endif