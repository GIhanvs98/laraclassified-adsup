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

namespace App\Http\Controllers\Web\Public;

use Illuminate\Support\Facades\Route;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Models\City;
use App\Models\SubAdmin2;
use Illuminate\Support\Facades\DB;

class HomeController extends FrontController
{
	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// dd('aaaaaaaaaaa');
		// Call API endpoint
		$endpoint = '/homeSections';
		$data = makeApiRequest('get', $endpoint);
		
		$message = $this->handleHttpError($data);
		$sections = (array)data_get($data, 'result.data');
		
		// Share sections' options in views,
		// that requires to be accessible everywhere in the app's views (including the master view).
		foreach ($sections as $section) {
			$optionName = data_get($section, 'method') . 'Op';
			view()->share($optionName, (array)data_get($section, $optionName));
		}
		
		$isFromHome = (
			!(config('larapen.core.api.client') === 'curl')
			|| str_contains(Route::currentRouteAction(), 'HomeController')
		);
		
		// Get SEO
		$getSearchFormOp = data_get($sections, 'getSearchForm.getSearchFormOp') ?? [];
		$this->setSeo($getSearchFormOp);

		$alldis= SubAdmin2::where('active', 1)->has('cities')->with('cities')->orderBy('name', 'asc')->get()->toArray();
		
		$tempArray = [];

		foreach($alldis as $alldi){

			$new = DB::table('subadmin2')->where("code", $alldi['code'])->first();
			
			$alldi['originalID'] = $new->id;

			array_push($tempArray, $alldi);

		}
		
		// dd($tempArray);
		return appView('home.index', compact('sections', 'isFromHome'), ['alldis' => $tempArray]);
	}
	
	/**
	 * Set SEO information
	 *
	 * @param array $getSearchFormOp
	 */
	private function setSeo(array $getSearchFormOp = []): void
	{
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('home');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		// Open Graph
		$this->og->title($title)->description($description);
		$ogImageUrl = config('settings.seo.og_image_url');
		if (empty($ogImageUrl)) {
			if (!empty(config('country.background_image_url'))) {
				$ogImageUrl = config('country.background_image_url');
			}
		}
		if (empty($ogImageUrl)) {
			if (!empty(data_get($getSearchFormOp, 'background_image_url'))) {
				$ogImageUrl = data_get($getSearchFormOp, 'background_image_url');
			}
		}
		if (!empty($ogImageUrl)) {
			if ($this->og->has('image')) {
				$this->og->forget('image')->forget('image:width')->forget('image:height');
			}
			$this->og->image($ogImageUrl, [
				'width'  => 600,
				'height' => 600,
			]);
		}
		view()->share('og', $this->og);
	}
}