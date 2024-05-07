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

namespace App\Http\Controllers\Web\Public\Account;

use Larapen\LaravelMetaTags\Facades\MetaTag;

class AdsReportsController extends AccountBaseController
{
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// Meta Tagsfalse
		MetaTag::set('title', 'Ads Reports');
		MetaTag::set('description', 'Ads Reports on', ['appName' => config('settings.app.name')]);
		
		return appView('account.ads-reports');
	}
}
