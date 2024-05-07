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

class ShopController extends AccountBaseController
{
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// Account panel. -> Not allowed for non members.
		 
        $user =  User::has('membership')->with('membership')->find(auth()->user()->id);

		// Validate membership not a free membership.
		$membershipDoesntExist = Membership::where('id', $user->membership->id)->member()->doesntExist();

		// Validate transactions. If the user has paid for the given time, then it is allowed. If he hasent payed for the current time.
		$transactionsValidDoesntExist = Transaction::valid('membership')
													->where('user_id', $user->id)
													->doesntExist();								
		// Meta Tags
		MetaTag::set('title', 'My Shop');
		MetaTag::set('description', 'My Shop on', ['appName' => config('settings.app.name')]);
		
		return appView('account.shop', compact('user', 'membershipDoesntExist', 'transactionsValidDoesntExist'));
	}

}
