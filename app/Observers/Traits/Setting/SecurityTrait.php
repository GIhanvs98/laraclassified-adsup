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

namespace App\Observers\Traits\Setting;

use Prologue\Alerts\Facades\Alert;

trait SecurityTrait
{
	/**
	 * Updating
	 *
	 * @param $setting
	 * @param $original
	 * @return false|void
	 */
	public function securityUpdating($setting, $original)
	{
		// password length
		$passwordMinLength = $setting->value['password_min_length'] ?? 6;
		$passwordMaxLength = $setting->value['password_max_length'] ?? 60;
		if ($passwordMinLength > $passwordMaxLength) {
			$message = trans('admin.min_max_error_message', ['attribute' => trans('admin.password_length')]);
			
			if (isAdminPanel()) {
				Alert::error($message)->flash();
			} else {
				flash($message)->error();
			}
			
			return false;
		}
		
		// Check if the PHP intl extension is installed
		// to use DNS or Spoof in the email validator.
		$emailValidatorDns = $setting->value['email_validator_dns'] ?? false;
		$emailValidatorSpoof = $setting->value['email_validator_spoof'] ?? false;
		if (($emailValidatorDns || $emailValidatorSpoof) && !extension_loaded('intl')) {
			$message = trans('admin.intl_extension_missing_error_message_for_email_validation');
			
			if (isAdminPanel()) {
				Alert::error($message)->flash();
			} else {
				flash($message)->error();
			}
			
			return false;
		}
	}
}
