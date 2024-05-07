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

namespace App\Http\Middleware;

use App\Helpers\UrlGen;
use App\Models\Blacklist;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Prologue\Alerts\Facades\Alert;

class BannedUser
{
	protected string $message = 'This user has been banned';
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @param $guard
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next, $guard = null)
	{
		// Exception for Install & Upgrade Routes
		if (str_contains(Route::currentRouteAction(), 'Web\Install\\')) {
			return $next($request);
		}
		
		$this->message = t($this->message);
		
		if (auth()->check()) {
			// Block the access if User is blocked (as registered User)
			$this->invalidateBlockedUser($request, $guard);
			
			// Block & Delete the access if User is banned (from Blacklist with its email address)
			$this->invalidateBannedUser($request);
		}
		
		return $next($request);
	}
	
	/**
	 * Block the access if User is blocked (as registered User)
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param $guard
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
	 */
	private function invalidateBlockedUser(Request $request, $guard = null)
	{
		if (auth()->guard($guard)->user()->blocked) {
			if ($request->ajax() || $request->wantsJson()) {
				return response($this->message, 401);
			} else {
				if (isAdminPanel()) {
					Alert::error($this->message)->flash();
					
					return redirect()->guest(admin_uri('login'));
				} else {
					flash($this->message)->error();
					
					return redirect()->guest(UrlGen::loginPath());
				}
			}
		}
	}
	
	/**
	 * Block & Delete the access if User is banned (from Blacklist with its email address)
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
	 */
	private function invalidateBannedUser(Request $request)
	{
		$cacheExpiration = (int)config('settings.optimization.cache_expiration', 86400);
		
		// Check if the user's email address has been banned
		$cacheId    = 'blacklist.email.' . auth()->user()->email;
		$bannedUser = cache()->remember($cacheId, $cacheExpiration, function () {
			return Blacklist::ofType('email')->where('entry', auth()->user()->email)->first();
		});
		
		if (!empty($bannedUser)) {
			$user = User::find(auth()->user()->id);
			$user->delete();
			
			if ($request->ajax() || $request->wantsJson()) {
				return response($this->message, 401);
			} else {
				if (isAdminPanel()) {
					Alert::error($this->message)->flash();
					
					return redirect()->guest(admin_uri('login'));
				} else {
					flash($this->message)->error();
					
					return redirect()->guest(UrlGen::loginPath());
				}
			}
		}
	}
}
