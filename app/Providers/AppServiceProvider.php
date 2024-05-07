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

namespace App\Providers;

use App\Helpers\SystemLocale;
use App\Models\Sanctum\PersonalAccessToken;
use App\Providers\AppService\AclSystemTrait;
use App\Providers\AppService\ConfigTrait;
use App\Providers\AppService\SymlinkTrait;
use App\Providers\AppService\TelescopeTrait;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
	use TelescopeTrait, AclSystemTrait, ConfigTrait, SymlinkTrait;

	private int $cacheExpiration = 86400; // Cache for 1 day (60 * 60 * 24)

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->runInspection();

		// Set Bootstrap as default client assets
		Paginator::useBootstrap();

		// Specified key was too long error
		try {
			Schema::defaultStringLength(191);
		} catch (\Throwable $e) {
		}

		// Setup Laravel Sanctum
		try {
			Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
		} catch (\Throwable $e) {
		}

		// Setup Storage Symlink
		$this->setupStorageSymlink();

		// Setup ACL system
		$this->setupAclSystem();

		// Setup Https
		$this->setupHttps();

		// Setup Configs
		$this->setupConfigs();

		// Date default encoding & translation
		// The translation option is overwritten when applying the front-end settings
		if (config('settings.app.date_force_utf8')) {
			Carbon::setUtf8(true);
		}

		// Set locale for PHP
		SystemLocale::setLocale(config('appLang.locale', 'en_US'));


		Validator::extend('unique_phone_format', function ($attribute, $value, $parameters, $validator) {

			if (empty ($value)) {
				return false; // Empty phone number field
			}

			$phone = null;

			// Remove any leading plus sign and replace with country code
			$phone = substr_replace($value, config('sms.country-code'), 0, 1);

			// Get the user by phone number from the database
			$user = User::where(['phone' => $phone, 'blocked' => 0, 'accept_terms' => 1, 'accept_marketing_offers' => 1])
				->whereNotNull('email_verified_at')
				->whereNotNull('phone_verified_at')
				->whereNotNull('deleted_at')
				->first();

			// If no user found, the phone number doesn't exist in the users table
			if (!$user) {
				return false;
			}

			return true;
		});

		Validator::replacer('unique_phone_format', function ($message, $attribute, $rule, $parameters) {
			return str_replace(':attribute', $attribute, trans('auth.failed'));
		});


		Validator::extend('unique_phone_except_self', function ($attribute, $value, $parameters, $validator) {

			if (empty ($value)) {
				return false; // Empty phone number field
			}

			$phone = null;

			// Remove any leading plus sign and replace with country code
			$phone = substr_replace($value, config('sms.country-code'), 0, 1);

			$currentUser = Auth::user();

			// Check if the contact number exists in the users table for any user except the current user
			$userCount = User::where('phone', $phone)
				->where('id', '!=', $currentUser->id)
				->whereNotNull('email_verified_at')
				->whereNotNull('phone_verified_at')
				->whereNotNull('deleted_at')
				->count();

			// If userCount is 0, the contact number doesn't exist in the users table for any user except the current user
			return $userCount === 0;
		});

		Validator::replacer('unique_phone_except_self', function ($message, $attribute, $rule, $parameters) {
			return str_replace(':attribute', $attribute, 'The :attribute is already taken by another user.');
		});

	}

	/**
	 * Setup Https
	 */
	private function setupHttps()
	{
		// Force HTTPS protocol
		if (config('larapen.core.forceHttps')) {
			URL::forceScheme('https');
		}
	}
}
