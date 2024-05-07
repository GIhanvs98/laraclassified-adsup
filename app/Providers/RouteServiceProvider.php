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

namespace App\Providers;

use App\Models\Category;
use App\Models\City;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * The path to your application's "home" route.
	 *
	 * Typically, users are redirected here after authentication.
	 *
	 * @var string
	 */
	public const HOME = '/account';

	/**
	 * Define your route model bindings, pattern filters, and other route configuration.
	 */
	public function boot(): void
	{

		Route::bind('category', function (string $value, $route) {

			$category = Category::whereId($value)->where('active', 1)->firstOrFail();

			if ($route->hasParameter('mainCategory')) {

				$mainCategoryId = $route->parameter('mainCategory');

				$query = Category::where('active', 1);

				if ($category->childrenClosure()->exists()) {

					// If the category is a parent and do have children.

					$query->whereId($value);

					$query->withWhereHas('children', function ($query) use ($mainCategoryId) {

						if ($mainCategoryId == 2 || $mainCategoryId == 5) {

							$query->whereIn('transaction_type', ['rent']);

						} else {

							$query->whereIn('transaction_type', ['sell', 'both']);

						}

						$query->where('active', 1);

					});

				}else if(!isset($category->parent_id)){

					// If the category is a empty parent.

					$query->whereId($category->id);

				} else {

					// If the category is a child.

					$query->whereId($category->parent_id);
				}

				$query->withWhereHas('subCategoryGroups', function ($query) use ($mainCategoryId) {

					$query->whereId($mainCategoryId);

				})->firstOrFail();

			}

			return $category;
		});

		Route::bind('location', function (string $value) {
			return City::whereId($value)->where('active', 1)->firstOrFail();
		});

		$this->configureRateLimiting();

		$this->routes(function () {
			// api
			Route::middleware('api')
				->namespace('App\Http\Controllers\Api')
				->prefix('api')
				->group(base_path('routes/api.php'));

			// web
			Route::middleware('web')
				->namespace('App\Http\Controllers\Web')
				->group(base_path('routes/web.php'));
		});
	}

	/**
	 * Configure the rate limiters for the application.
	 */
	protected function configureRateLimiting(): void
	{
		// More Info: https://laravel.com/docs/10.x/routing#rate-limiting

		// API rate limit
		RateLimiter::for('api', function (Request $request) {
			// Exception for local and demo environments
			if (isLocalEnv() || isDemoEnv()) {
				return isLocalEnv()
					? Limit::none()
					: (
						$request->user()
						? Limit::perMinute(90)->by($request->user()->id)
						: Limit::perMinute(60)->by($request->ip())
					);
			}

			// Limits access to the routes associated with it to:
			// - (For logged users): 1200 requests per minute by user ID
			// - (For guests): 600 requests per minute by IP address
			return $request->user()
				? Limit::perMinute(1200)->by($request->user()->id)
				: Limit::perMinute(600)->by($request->ip());
		});

		// Global rate limit (Not used)
		RateLimiter::for('global', function (Request $request) {
			// Limits access to the routes associated with it to:
			// - 1000 requests per minute
			return Limit::perMinute(1000);
		});
	}
}
