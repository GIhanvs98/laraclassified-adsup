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

use App\Http\Controllers\Web\Public\Auth\Traits\VerificationTrait;
use App\Http\Requests\AvatarRequest;
use App\Http\Requests\UserRequest;
use App\Models\Gender;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class EditController extends AccountBaseController
{
	use VerificationTrait;
	
	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$data = [];
		
		$data['genders'] = Gender::query()->get();
		
		// Meta Tags
		MetaTag::set('title', t('my_account'));
		MetaTag::set('description', t('my_account_on', ['appName' => config('settings.app.name')]));
		
		return appView('account.edit', $data);
	}
	
	/**
	 * @param \App\Http\Requests\UserRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updateDetails(UserRequest $request)
	{
		// Call API endpoint
		$endpoint = '/users/' . auth()->user()->id;
		$data = makeApiRequest('put', $endpoint, $request->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			flash($message)->error();
			
			return redirect()->back()->withInput($request->except(['photo']));
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
		} else {
			flash($message)->error();
		}
		
		// Get User Resource
		$user = data_get($data, 'result');
		
		// Don't log out the User (See User model)
		if (data_get($data, 'extra.emailOrPhoneChanged')) {
			session()->put('emailOrPhoneChanged', true);
		}
		
		// Get Query String
		$queryString = '';
		if ($request->filled('panel')) {
			$queryString = '?panel=' . $request->input('panel');
		}
		
		// Get the next URL
		$nextUrl = url('account' . $queryString);
		
		if (
			data_get($data, 'extra.sendEmailVerification.emailVerificationSent')
			|| data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')
		) {
			session()->put('userNextUrl', $nextUrl);
			
			if (data_get($data, 'extra.sendEmailVerification.emailVerificationSent')) {
				session()->put('emailVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationEmailLink($user, 'users');
			}
			
			if (data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')) {
				session()->put('phoneVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationSmsLink($user, 'users');
				
				// Go to Phone Number verification
				$nextUrl = url('users/verify/phone/');
			}
		}
		
		// Mail Notification Message
		if (data_get($data, 'extra.mail.message')) {
			$mailMessage = data_get($data, 'extra.mail.message');
			if (data_get($data, 'extra.mail.success')) {
				flash($mailMessage)->success();
			} else {
				flash($mailMessage)->error();
			}
		}
		
		return redirect($nextUrl);
	}
	
	/**
	 * Update the User's photo.
	 *
	 * @param \App\Http\Requests\AvatarRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updatePhoto(AvatarRequest $request)
	{
		// Call API endpoint
		$endpoint = '/users/' . auth()->user()->id;
		$data = makeApiRequest('put', $endpoint, $request->all(), [], true);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			// AJAX Response
			if ($request->ajax()) {
				return response()->json(['error' => $message]);
			}
			
			flash($message)->error();
			
			return redirect(url('account'))->withInput();
		}
		
		// AJAX Response
		if ($request->ajax()) {
			if (!data_get($data, 'success')) {
				return response()->json(['error' => $message]);
			}
			
			$fileInput = data_get($data, 'extra.photo.extra.fileInput');
			
			return response()->json($fileInput);
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
		} else {
			flash($message)->error();
		}
		
		return redirect(url('account'));
	}
}
