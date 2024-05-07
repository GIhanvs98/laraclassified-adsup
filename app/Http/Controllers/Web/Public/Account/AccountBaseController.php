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

use App\Http\Controllers\Web\Public\FrontController;

abstract class AccountBaseController extends FrontController
{
	/**
	 * AccountBaseController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(function ($request, $next) {
			if (auth()->check()) {
				$this->leftMenuInfo();
			}
			
			return $next($request);
		});
		
		// Get Page Current Path
		$pagePath = (request()->segment(1) == 'account') ? (request()->segment(3) ?? '') : '';
		view()->share('pagePath', $pagePath);
	}
	
	public function leftMenuInfo(): void
	{
		// Share User Info
		view()->share('user', auth()->user());
		
		// Get user's stats - Call API endpoint
		$endpoint = '/users/' . auth()->user()->getAuthIdentifier() . '/stats';
		$data = makeApiRequest('get', $endpoint);
		
		$stats = data_get($data, 'result');
		view()->share('stats', $stats);
	}
}
