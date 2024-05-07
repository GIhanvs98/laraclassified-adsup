<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Helpers\UrlGen;
use App\Traits\ContactNumbersTrait;
use App\Http\Controllers\Api\Auth\Traits\CheckIfAuthFieldIsVerified;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    use ContactNumbersTrait, CheckIfAuthFieldIsVerified;

	protected $loginPath = 'login';

    public bool $emailSection = false;

    #[Validate('required', as: 'email')]
    public $email = '';
 
    #[Validate('required', as: 'password')]
    public $password = '';
 
    public bool $remember_me = false;

    public $redirectTo;

    public $errorMessages;

    public function mount(){

		// Set default URLs
		if (str_contains(url()->previous(), '/' . admin_uri())) {

			$this->redirectTo = admin_uri();

		}else if (!str_contains(url()->previous(), route('home')) && !str_contains(url()->previous(), config('routes.login', 'login'))) {

			$this->redirectTo = url()->previous();

        } else {

			$this->redirectTo = route('account');
		}

        $this->contact_numbers = [ 
            'main_contact_number' => [
                'id' => 'main_contact_number',
                'attribute_name' => 'Contact Number',
                'models' => [
                    'contact_number' => 'contact_numbers.main_contact_number.values.contact_number',
                    'otp' => 'contact_numbers.main_contact_number.values.otp',
                ],
                'values' => [
                    'contact_number' => null,
                    'otp' => null,
                ],
                'rules' => [
                    'contact_number' => [
                        'required',
                        'numeric',
                        'min:10',
                        'unique_phone_format',
                    ],
                    'otp' => [
                        'required',
                        'numeric',
                        'digits:6',
                    ],
                ],
                'properties' => [
                    'otp_session_key' => 'contact_numbers.main_contact_number.otp',
                    'permanent' => true,
                ],
                'states' => [
                    'stage' => null,
                    'visibility' => true,
                ],
            ]
        ];

    }
 
    public function continueWithEmail(){

        $this->errorMessages = null;

        $this->emailSection = true;
    }

    public function back(){

        $this->errorMessages = null;

        $this->emailSection = false;
    }

    /*
    public function api_login(){

        $this->errorMessages = null;
        
        $this->validate();
        
        $request = [
            'email' => $this->email, 
            'password' => $this->password, 
            'remember_me' => $this->remember_me,
            'country' => config('country.code'),
            'auth_field' => 'email',
        ];

        try {
                
            // Call API endpoint
            $endpoint = '/auth/login';
            $data = makeApiRequest('post', $endpoint, $request);

            // Response for successful login
            if (
                data_get($data, 'isSuccessful')
                && data_get($data, 'success')
                && !empty (data_get($data, 'extra.authToken'))
                && !empty (data_get($data, 'result.id'))
            ) {
                auth()->loginUsingId(data_get($data, 'result.id'));
                session()->put('authToken', data_get($data, 'extra.authToken'));

                if (data_get($data, 'extra.isAdmin')) {
                    return redirect(admin_uri());
                }

                return redirect($this->redirectTo);
            }

            $errorMessages = data_get($data, 'message', trans('auth.failed'));

            $this->errorMessages = $errorMessages;

        } catch (\Throwable $th) {
            $this->errorMessages = config('app.debug') ? $th->getMessage() : "Unexpected error occured please try again!";
        }
    }
    */

    public function login(){

        $this->errorMessages = null;
        
        $credentials = $this->validate();

		try {
			
			$dbField = getAuthFieldFromItsValue($this->email) ?? 'email';

            if (auth()->attempt([$dbField => $credentials['email'], 'password' => $credentials['password'], 'blocked' => 0], $this->remember_me)) {

				$authUser = auth()->user();

				// Get the user as model object
				$user = User::find($authUser->getAuthIdentifier());

				// Is user has verified login?
				$tmpData = $this->userHasVerifiedLogin($authUser, $user, 'email');
				$isSuccess = array_key_exists('success', $tmpData) && $tmpData['success'];
				
				// Send the right error message (with possibility to re-send verification code)
				if (!$isSuccess) {
					if (
						array_key_exists('success', $tmpData)
						&& array_key_exists('message', $tmpData)
						&& array_key_exists('extra', $tmpData)
					) {
						return abort(403);
					}
					
                    return $this->errorMessages = trans('auth.failed');
				}

				// Redirect admin users to the Admin panel
				$isAdmin = $user->hasAllPermissions(Permission::getStaffPermissions());
				
				// Revoke previous tokens
				$user->tokens()->delete();
				
				// Create the API access token
				$token = $user->createToken('Desktop Web');
            
                auth()->loginUsingId($user->id);

                session()->put('authToken', $token->plainTextToken);

                if ($isAdmin) {

                    return redirect(admin_uri());
                }

                return redirect($this->redirectTo);

            }

		} catch (\Throwable $e) {

            $errorMessage = config('app.debug') ? $e->getMessage() : trans('auth.failed');
		}
		
		return $this->errorMessages = $errorMessage ?? trans('auth.failed');
    }

    public function contactNumberLoginCallback(){

        $this->errorMessages = null;

        $rules = [];

        $field = $this->contact_numbers['main_contact_number'];

        $rules[$field['models']['contact_number']] = $field['rules']['contact_number'] ?? null;

        $rules[$field['models']['otp']] = $field['rules']['otp'] ?? null;
        
        $attributes = [];

        $attributes[$field['models']['contact_number']] = strtolower($field['attribute_name'] ?? 'Contact Number');

        $attributes[$field['models']['otp']] = 'otp';

        $this->validate(
            rules: $rules,
            attributes: $attributes,
        );
        
        if($field['states']['stage']  === 'success'){

            try {

                $phone = $this->formatPhoneNumber($field['values']['contact_number'] ?? null, config('sms.country-code'));

                $user = User::where(['phone' => $phone, 'blocked' => 0, 'accept_terms' => 1, 'accept_marketing_offers' => 1])
                    ->whereNotNull('email_verified_at')
                    ->whereNotNull('phone_verified_at')
                    ->whereNotNull('deleted_at')
                    ->first();

                $userLogged = ($user) ? auth()->setUser($user) : null;

                if ($userLogged) {

                    $authUser = auth()->user();

                    // Get the user as model object
                    $user = User::find($authUser->getAuthIdentifier());

                    // Is user has verified login?
                    $tmpData = $this->userHasVerifiedLogin($authUser, $user, 'email');
                    $isSuccess = array_key_exists('success', $tmpData) && $tmpData['success'];
                    
                    // Send the right error message (with possibility to re-send verification code)
                    if (!$isSuccess) {
                        if (
                            array_key_exists('success', $tmpData)
                            && array_key_exists('message', $tmpData)
                            && array_key_exists('extra', $tmpData)
                        ) {
                            return abort(403);
                        }
                        
                        return $this->errorMessages = trans('auth.failed');
                    }

                    // Redirect admin users to the Admin panel
                    $isAdmin = $user->hasAllPermissions(Permission::getStaffPermissions());
                    
                    // Revoke previous tokens
                    $user->tokens()->delete();
                    
                    // Create the API access token
                    $token = $user->createToken('Desktop Web');
                
                    auth()->loginUsingId($user->id);

                    session()->put('authToken', $token->plainTextToken);

                    if ($isAdmin) {

                        return redirect(admin_uri());
                    }

                    return redirect($this->redirectTo);

                }

            } catch (\Throwable $e) {

                $errorMessage = config('app.debug') ? $e->getMessage() : "Oops. Something went wrong. Please try again.";
            }
            
            return $this->errorMessages = $errorMessage ?? trans('auth.failed');
        }

    }

}; 

?>

<div class="main-container">

    <div class="container bg-white" style="max-width: 820px !important; border-radius: 4px;">
        <div class="row login_page_wrapper">

            <div class="col-md-6 d-md-block d-none login">
                <h1>Welcome to {{ config('app.name') }}</h1>
                <p>Please Login to manage your account. You can view your ads and account details.</p>
                <ul class="account-options">
                    <li><i class="fa fa-tag" aria-hidden="true"></i> Post your new ads.</li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="fa" style="width: 56px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        Manage your shop.
                    </li>
                    <li><i class="fa fa-star" aria-hidden="true"></i> Keep track of your ads.</li>
                </ul>
            </div>

            <div class="col-md-6 col-12 login-form-container">
                
                @if($emailSection)

                    <a wire:click="back" class="d-inline-block btn btn-link" style="margin-bottom: 20px; margin-top: 12px; padding: 0px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="height: 16px;margin-top: -5px;margin-right: 5px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Back
                    </a>

                    <form wire:submit="login">

                        @if (isset($errorMessages))
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible mb-4">
                                    <button type="button" class="btn-close" x-on:click="$wire.errorMessages = ''" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
                                    {{ $errorMessages }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="text" wire:model.blur="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter your email">
                            <div class="invalid-feedback">@error('email') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" wire:model.blur="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password">
                            <div class="invalid-feedback">@error('password') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="remember_me" id="rememberMe">
                                <label class="form-check-label" for="rememberMe" style="cursor: pointer">Remember me</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">Login</button>

                        <a class="link-opacity-75-hover forgot-password mt-2 mb-3" href="{{ url('password/reset') }}">Forgot password?</a>
                            
                        <div>
                            <span>Do not have an account</span>
                            <a href="{{ \App\Helpers\UrlGen::register() }}">Sign up</a>
                        </div>
                        
                    </form>

                @else
                
                    <div style="margin-top: 24px;">

                        @if (isset($errorMessages))
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible mb-4">
                                    <button type="button" class="btn-close" x-on:click="$wire.errorMessages = ''" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
                                    {{ $errorMessages }}
                                </div>
                            </div>
                        @endif
                        
                        @include('livewire.inc.auth-section.phone', [
                            'title' => $contact_numbers['main_contact_number']['attribute_name'],
                            'field' => $contact_numbers['main_contact_number'],
                        ])

                        <!-- social login -->
                        @includeFirst([config('larapen.core.customizedViewPath') . 'auth.login.inc.social-rectangle', 'auth.login.inc.social-rectangle'])

                        <button wire:click="continueWithEmail" class="btn btn-primary btn-block d-flex justify-content-center items-center" type="button">
                            <div style="fill: #fff; padding-right: 5px;">
                                <svg viewBox="0 0 24 24" width="18" class="email-svg">
                                    <path d="M24 3.79l-9 10.05a4.22 4.22 0 0 1-3 1.64 4.36 4.36 0 0 1-3-1.7L0 3.79v13.68a8.94 8.94 0 0 0 .1 1.27l5.23-5.37 1.34 1.35-5.34 5.37a10.11 10.11 0 0 0 1.34.12H22a1.69 1.69 0 0 0 .53-.12l-5.2-5.37 1.34-1.35 5.15 5.37a5.52 5.52 0 0 0 .18-1.52V3.79zm-11.24 9.27l8.57-9.27H2.67l8.57 9.27a1.26 1.26 0 0 0 .76.44 1.26 1.26 0 0 0 .76-.44z" fill-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span>Continue with Email</span>
                        </button>

                        @if(url('/page/terms'))
                            <div class="terms">
                                <div>By signing up for an account you agree to our</div>
                                <a target="_blank" href="{{ url('/page/terms') }}">Terms and Conditions</a>
                            </div>
                        @endif
                        
                    </div>
                    
                @endif

            </div>

        </div>
    </div>

    <style>

        .main-container{
            padding: 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 10px;
            min-height: 764px;
        }

        @media screen and (max-height: 764px){

            .main-container{
                min-height: calc(100dvh - 86px);
            }

        }

        .login_page_wrapper {
            background: #fff;
            padding: 18px;
        }

        .login h1{
            font-size: 24px;
            font-family: open sans,pf dintext pro,Arial,Helvetica,sans-serif;
            font-weight: 400;
            color: #2f3432;
            margin: 24px 0 16px;
            padding-bottom: 0px;
        }

        .login p{
            color: #707676;
        }
        
        .account-options {
            list-style: none;
            margin-top: 40px;
            padding-left: 0;
        }
        .account-options li {
            margin-bottom: 20px;
        }

        .account-options li .fa {
            font-size: 24px;
            padding: 15px;
            margin-right: 10px;
            border: 1px solid #edefef;
            border-radius: 35px;
        }

        .account-options li .fa-tag {
            color: #bd7a7a;
        }

        .account-options li .fa-star {
            color: #e4c20d;
        }

        .login-form-container {
            border-left: 1px solid #e7edee;
            padding: 0px 14px 0 24px;
        }

        @media screen and (max-width: 767px){

            .login-form-container {
                border-left: 0px;
                padding: 0px 10px 0 10px;
            }

        }

        .login-form-container h5{
            font-family: 'Open Sans', 'PF DinText Pro', Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: 700;
            line-height: 19px;
            color: #2f3432;
        }

        .login-form-container .or{
            margin-bottom: 16px;
            margin-top: 16px;
            font-weight: 700;
            text-align: center;
            color: #afb7ad;
        }

        .tertiary--5kHib {
            color: #707676;
            background-color: #f3f6f5;
            border: 1px solid #d4ded9;
            font-weight: 800;
        }

        .social-login-bt{
            margin-bottom: 15px;
        }

        .forgot-password{
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .terms{
            text-align: center;
            font-size: 12px;
            color: #707676;
            margin-top: 20px;
        }
    </style>

</div>