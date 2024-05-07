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

namespace App\Rules;

use App\Models\Blacklist;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class BlacklistPhoneRule implements Rule
{
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$value = trim(strtolower($value));
		$value = ltrim($value, '+');
		$valueWithPrefix = '+' . $value;
		
		// Banned phone number
		$blacklisted = Blacklist::ofType('phone')
			->where(function ($query) use ($value, $valueWithPrefix) {
				$query->where('entry', $value)->orWhere('entry', $valueWithPrefix);
			})->first();
		if (!empty($blacklisted)) {
			return false;
		}
		
		// Blocked user's phone number
		$user = User::withoutGlobalScopes([VerifiedScope::class])
			->where(function ($query) use ($value, $valueWithPrefix) {
				$query->where('phone', $value)->orWhere('phone', $valueWithPrefix);
			})->where('blocked', 1)->first();
		if (!empty($user)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return trans('validation.blacklist_phone_rule');
	}
}
