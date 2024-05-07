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

namespace App\Http\Controllers\Web\Public\Auth;

use App\Http\Controllers\Web\Public\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Web\Public\FrontController;
use App\Http\Requests\UserRequest;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class RegisterController extends FrontController
{
	use VerificationTrait;
	
	/**
	 * Where to redirect users after login / registration.
	 *
	 * @var string
	 */
	protected string $redirectTo = '/account';
	
	/**
	 * RegisterController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			
			return $next($request);
		});
	}
	
	/**
	 * Common Queries
	 */
	public function commonQueries()
	{
		$this->redirectTo = 'account';
	}
	
	/**
	 * Show the form the create a new user account.
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function showRegistrationForm()
	{
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('register');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		return appView('auth.register.index');
	}
	
	/**
	 * Register a new user account.
	 *
	 * @param \App\Http\Requests\UserRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function register(UserRequest $request)
	{
		// Call API endpoint
		$endpoint = '/users';
		$data = makeApiRequest('post', $endpoint, $request->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			return back()->withErrors(['error' => $message])->withInput();
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			session()->put('message', $message);
		} else {
			flash($message)->error();
		}
		
		// Get User Resource
		$user = data_get($data, 'result');
		
		// Get the next URL
		$nextUrl = url('register/finish');
		
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
		} else {
			if (!empty(data_get($data, 'extra.authToken')) && !empty(data_get($user, 'id'))) {
				// Auto logged in the User
				if (auth()->loginUsingId(data_get($data, 'result.id'))) {
					session()->put('authToken', data_get($data, 'extra.authToken'));
					$nextUrl = url('account');
				}
			}
		}
		
		return redirect($nextUrl);
	}
	
	/**
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function finish()
	{
		if (!session()->has('message')) {
			return redirect('/');
		}
		
		// Meta Tags
		MetaTag::set('title', session()->get('message'));
		MetaTag::set('description', session()->get('message'));
		
		return appView('auth.register.finish');
	}
}
