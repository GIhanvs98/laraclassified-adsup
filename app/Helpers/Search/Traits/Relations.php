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

namespace App\Helpers\Search\Traits;

use App\Helpers\Search\Traits\Relations\CategoryRelation;
use App\Helpers\Search\Traits\Relations\PaymentRelation;

trait Relations
{
	use CategoryRelation, PaymentRelation;
	
	protected function setRelations(): void
	{
		if (!isset($this->posts)) {
			abort(500, 'Fatal Error: Search relations cannot be applied.');
		}
		
		// category
		$this->setCategoryRelation();
		
		// postType
		$this->posts->with('postType');
		
		// latestPayment
		$this->setPaymentRelation();
		
		// city
		$this->posts->with('city')->has('city');
		
		// pictures
		$this->posts->with('pictures');
		
		// user
		$this->posts->with([
			'user',
			'user.permissions',
			'user.roles',
		]);
		
		// savedByLoggedUser
		$this->posts->with('savedByLoggedUser');
	}
}
