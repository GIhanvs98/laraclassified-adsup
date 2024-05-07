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

class TransactionsController extends AccountBaseController
{
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// Call API endpoint
		$endpoint = '/payments';
		$queryParams = [
			'embed' => 'post,paymentMethod,package,currency',
			'sort'  => 'created_at',
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');

		$transactions = auth()->user()->transactions()->onepayTransactions()->orderBy('updated_at', 'desc')->get();

		// Meta Tags
		MetaTag::set('title', t('My Transactions'));
		MetaTag::set('description', t('My Transactions on', ['appName' => config('settings.app.name')]));
		
		return appView('account.transactions', compact('apiResult', 'apiMessage', 'transactions'));
	}
}
