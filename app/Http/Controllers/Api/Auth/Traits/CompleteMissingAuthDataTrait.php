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

namespace App\Http\Controllers\Api\Auth\Traits;

use App\Helpers\Arr;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;

trait CompleteMissingAuthDataTrait
{
	/**
	 * Use the listing data to complete the user's missing auth data
	 * NOTE: $user need to null or verified user model instance
	 *
	 * @param $post
	 * @param \App\Models\User|null $user
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function completeMissingAuthData($post = null, User|null $user = null): \Illuminate\Http\JsonResponse
	{
		$data = [
			'success' => false,
			'message' => null,
		];
		
		$isAuthUser = false;
		if (empty($user)) {
			$guard = isFromApi() ? 'sanctum' : null;
			if (auth($guard)->check()) {
				$user = auth($guard)->user();
				$isAuthUser = true;
			}
		}
		
		if (
			empty($user)
			|| !Arr::keyExists('id', $user)
			|| !Arr::keyExists('email', $user)
			|| !Arr::keyExists('phone', $user)
		) {
			return $this->apiResponse($data);
		}
		
		$isVerifiedEmail = false;
		$isVerifiedPhone = false;
		
		if (!empty($post)) {
			$isVerifiedEmail = (
				config('settings.mail.email_verification') == '1'
				&& !empty($post->email)
				&& !empty($post->email_verified_at)
			);
			$isVerifiedPhone = (
				config('settings.sms.phone_verification') == '1'
				&& !empty($post->phone)
				&& !empty($post->phone_verified_at)
			);
			
			// From filled entity (listings : create|update)
			$email = $isVerifiedEmail ? $post->email : null;
			$phone = $isVerifiedPhone ? $post->phone : null;
		} else {
			// From input (When a user contact an author)
			$email = request()->input('email');
			$phone = request()->input('phone');
		}
		
		if (empty($email) && empty($phone)) {
			return $this->apiResponse($data);
		}
		
		// Don't make any DB query if filled data is the same as auth user data
		if ($user->email == $email && $user->phone == $phone) {
			return $this->apiResponse($data);
		}
		
		// Get (verified) user instance that be saved
		if ($isAuthUser) {
			$user = User::where('id', $user->id)->first();
		}
		
		if (empty($user)) {
			return $this->apiResponse($data);
		}
		
		$message = null;
		
		// Complete missing email address
		if (empty($user->email) && !empty($email)) {
			$emailDoesntExist = User::withoutGlobalScopes([VerifiedScope::class])->where('email', $email)->doesntExist();
			if ($emailDoesntExist) {
				$user->email = $email;
				$user->email_verified_at = $isVerifiedEmail ? now() : null;
				$message = t('email_completed');
			}
		}
		
		// Complete missing phone number
		if (empty($user->phone) && !empty($phone)) {
			$phoneDoesntExist = User::withoutGlobalScopes([VerifiedScope::class])->where('phone', $phone)->doesntExist();
			if ($phoneDoesntExist) {
				$user->phone = $phone;
				$user->phone_verified_at = $isVerifiedPhone ? now() : null;
				$message = t('phone_completed');
			}
		}
		
		if ($user->isDirty()) {
			$user->save();
			
			$data['success'] = true;
			$data['message'] = $message;
		}
		
		return $this->apiResponse($data);
	}
}
