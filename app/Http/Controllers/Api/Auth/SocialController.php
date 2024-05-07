<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Ip;
use App\Http\Controllers\Api\BaseController;
use App\Models\Blacklist;
use App\Models\Permission;
use App\Models\Post;
use App\Models\User;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Notifications\SendPasswordAndVerificationInfo;
use App\Notifications\UserNotification;
use App\Http\Controllers\Api\Auth\Helpers\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;

/**
 * @group Social Auth
 */
class SocialController extends BaseController
{
	use AuthenticatesUsers;
	
	// Supported Providers
	// Stateless authentication is not available for the Twitter driver, which uses OAuth 1.0 for authentication.
	private array $network = ['facebook', 'linkedin', 'google'];
	private array $networkChecker;
	private string $serviceError = "Unknown error. The service does not work.";
	
	/**
	 * SocialController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Providers Checker
		$this->networkChecker = [
			'facebook' => (config('settings.social_auth.facebook_client_id') && config('settings.social_auth.facebook_client_secret')),
			'linkedin' => (config('settings.social_auth.linkedin_client_id') && config('settings.social_auth.linkedin_client_secret')),
			'twitter'  => (config('settings.social_auth.twitter_client_id') && config('settings.social_auth.twitter_client_secret')),
			'google'   => (config('settings.social_auth.google_client_id') && config('settings.social_auth.google_client_secret')),
		];
	}
	
	/**
	 * Get target URL
	 *
	 * @urlParam provider string required The provider's name - Possible values: facebook, linkedin, or google. Example: null
	 *
	 * @param string $provider
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getProviderTargetUrl(string $provider): \Illuminate\Http\JsonResponse
	{
		// Get the Provider and verify that if it's supported
		if (!in_array($provider, $this->network)) {
			return $this->respondNotFound();
		}
		
		// Check if the Provider is enabled
		$providerIsEnabled = (array_key_exists($provider, $this->networkChecker) && $this->networkChecker[$provider]);
		if (!$providerIsEnabled) {
			return $this->respondNotFound();
		}
		
		// Redirect to the Provider's website
		try {
			return Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
		} catch (\Throwable $e) {
			$message = $e->getMessage();
			if (empty($message)) {
				$message = $this->serviceError;
			}
			
			return $this->respondError($message);
		}
	}
	
	/**
	 * Get user info
	 *
	 * @urlParam provider string required The provider's name - Possible values: facebook, linkedin, or google. Example: null
	 *
	 * @param string $provider
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function handleProviderCallback(string $provider): \Illuminate\Http\JsonResponse
	{
		// Get the Provider and verify that if it's supported
		if (!in_array($provider, $this->network)) {
			return $this->respondNotFound();
		}
		
		$data = [];
		$extra = [];
		
		// API CALL - GET USER FROM PROVIDER
		try {
			// $userData = Socialite::driver($provider)->stateless()->user();
			$token = request()->input('accessToken');
			$userData = Socialite::driver($provider)->stateless()->userFromToken($token);
			
			// Data not found
			if (!$userData) {
				return $this->respondError(t('unknown_error_please_try_again'));
			}
			
			// Email not found
			if (!filter_var($userData->getEmail(), FILTER_VALIDATE_EMAIL)) {
				return $this->respondError(t('email_not_found_at_provider', ['provider' => mb_ucfirst($provider)]));
			}
		} catch (\Throwable $e) {
			$message = $e->getMessage();
			if (empty($message)) {
				$message = $this->serviceError;
			}
			
			return $this->respondError($message);
		}
		
		// Debug
		// dd($userData);
		
		// DATA MAPPING
		try {
			$mapUser = [];
			
			// Get the user's Name (First Name & Last Name)
			$mapUser['name'] = (isset($userData->name) && is_string($userData->name)) ? $userData->name : '';
			if ($mapUser['name'] == '') {
				// facebook
				if (isset($userData->user['first_name']) && isset($userData->user['last_name'])) {
					$mapUser['name'] = $userData->user['first_name'] . ' ' . $userData->user['last_name'];
				}
			}
			if ($mapUser['name'] == '') {
				// linkedin
				$mapUser['name'] = (isset($userData->user['formattedName'])) ? $userData->user['formattedName'] : '';
				if ($mapUser['name'] == '') {
					if (isset($userData->user['firstName']) && isset($userData->user['lastName'])) {
						$mapUser['name'] = $userData->user['firstName'] . ' ' . $userData->user['lastName'];
					}
				}
			}
			
			// Check if the user's email address has been banned
			$bannedUser = Blacklist::ofType('email')->where('entry', $userData->getEmail())->first();
			if (!empty($bannedUser)) {
				return $this->respondError(t('This user has been banned'));
			}
			
			// GET LOCAL USER
			$user = User::withoutGlobalScopes([VerifiedScope::class])->where('provider', $provider)->where('provider_id', $userData->getId())->first();
			
			// CREATE LOCAL USER IF DON'T EXISTS
			if (empty($user)) {
				// Before... Check if user has not signup with an email
				$user = User::withoutGlobalScopes([VerifiedScope::class])->where('email', $userData->getEmail())->first();
				if (empty($user)) {
					// Generate random password
					$randomPassword = getRandomPassword(8);
					
					// Register the User (As New User)
					$userInfo = [
						'country_code'      => config('country.code', config('ipCountry.code')),
						'language_code'     => config('app.locale'),
						'name'              => $mapUser['name'],
						'auth_field'        => 'email',
						'email'             => $userData->getEmail(),
						'password'          => Hash::make($randomPassword),
						'ip_addr'           => Ip::get(),
						'email_verified_at' => now(),
						'phone_verified_at' => now(),
						'provider'          => $provider,
						'provider_id'       => $userData->getId(),
						'created_at'        => now()->format('Y-m-d H:i:s'),
					];
					$user = new User($userInfo);
					$user->save();
					
					// Send Generated Password by Email
					try {
						$user->notify(new SendPasswordAndVerificationInfo($user, $randomPassword));
					} catch (\Throwable $e) {
					}
					
					// Update Listings created by this email
					if (isset($user->id) && $user->id > 0) {
						Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->where('email', $userInfo['email'])->update(['user_id' => $user->id]);
					}
					
					// Send Admin Notification Email
					if (config('settings.mail.admin_notification') == 1) {
						try {
							// Get all admin users
							$admins = User::permission(Permission::getStaffPermissions())->get();
							if ($admins->count() > 0) {
								Notification::send($admins, new UserNotification($user));
							}
						} catch (\Throwable $e) {
							return $this->respondError($e->getMessage());
						}
					}
					
				} else {
					// Update 'created_at' if empty (for time ago module)
					if (empty($user->created_at)) {
						$user->created_at = now()->format('Y-m-d H:i:s');
					}
					$user->email_verified_at = now();
					$user->phone_verified_at = now();
					$user->save();
				}
			} else {
				// Update 'created_at' if empty (for time ago module)
				if (empty($user->created_at)) {
					$user->created_at = now()->format('Y-m-d H:i:s');
				}
				$user->email_verified_at = now();
				$user->phone_verified_at = now();
				$user->save();
			}
			
			// GET A SESSION FOR USER
			if (auth()->loginUsingId($user->id)) {
				$data['success'] = true;
				$data['result'] = null;
				
				// Create the API access token
				$deviceName = ucfirst($provider);
				$token = $user->createToken($deviceName);
				
				$extra['authToken'] = $token->plainTextToken;
				$extra['tokenType'] = 'Bearer';
				
				$data['extra'] = $extra;
				
				return $this->apiResponse($data);
			} else {
				return $this->respondError(t('Error on user\'s login.'));
			}
		} catch (\Throwable $e) {
			$message = $e->getMessage();
			if (empty($message)) {
				$message = $this->serviceError;
			}
			
			return $this->respondError($message);
		}
	}
}
