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
use App\Models\User;
use App\Models\Membership;
use App\Models\Transaction;

class MembershipController extends AccountBaseController
{
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{										// Meta Tags
		MetaTag::set('title', 'My Membership');
		MetaTag::set('description', 'My Membership on', ['appName' => config('settings.app.name')]);
		
		return appView('account.membership');
	}
}
