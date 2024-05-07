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

use App\Helpers\UrlGen;
use App\Http\Requests\ContactRequest;
use App\Models\City;
use App\Models\Membership;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class PageController extends FrontController
{
	/**
	 * @return \Illuminate\Contracts\View\View
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function pricing()
	{
		// Get Packages - Call API endpoint
		$endpoint = '/packages';
		$queryParams = [
			'embed' => 'currency',
			'sort'  => '-lft',
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$message = $this->handleHttpError($data);
		$packages = data_get($data, 'result.data');
		
		// Select a Package and go to previous URL ----------------------
		// Add Listing possible URLs
		$addListingUriArray = [
			'create',
			'post\/create',
			'post\/create\/[^\/]+\/photos',
		];
		// Default Add Listing URL
		$addListingUrl = UrlGen::addPost();
		if (request()->filled('from')) {
			foreach ($addListingUriArray as $uriPattern) {
				if (preg_match('#' . $uriPattern . '#', request()->get('from'))) {
					$addListingUrl = url(request()->get('from'));
					break;
				}
			}
		}
		view()->share('addListingUrl', $addListingUrl);
		// --------------------------------------------------------------
		
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('pricing');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		// Open Graph
		$this->og->title($title)->description($description)->type('website');
		view()->share('og', $this->og);
		
		$memberships = Membership::member()->get();

		$nonMembership = Membership::nonMember()->first();

		return appView('pages.pricing', compact('packages', 'memberships', 'nonMembership', 'message'));
	}
	
	/**
	 * @param $slug
	 * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
	 */
	public function cms($slug)
	{
		// Get Packages - Call API endpoint
		$endpoint = '/pages/' . $slug;
		$data = makeApiRequest('get', $endpoint);
		
		$message = $this->handleHttpError($data);
		$page = data_get($data, 'result');
		
		// Check if an external link is available
		if (!empty(data_get($page, 'external_link'))) {
			return redirect()->away(data_get($page, 'external_link'), 301)->withHeaders(config('larapen.core.noCacheHeaders'));
		}
		
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('staticPage');
		$title = str_replace('{page.title}', data_get($page, 'seo_title'), $title);
		$title = str_replace('{app.name}', config('app.name'), $title);
		$title = str_replace('{country.name}', config('country.name'), $title);
		
		$description = str_replace('{page.description}', data_get($page, 'seo_description'), $description);
		$description = str_replace('{app.name}', config('app.name'), $description);
		$description = str_replace('{country.name}', config('country.name'), $description);
		
		$keywords = str_replace('{page.keywords}', data_get($page, 'seo_keywords'), $keywords);
		$keywords = str_replace('{app.name}', config('app.name'), $keywords);
		$keywords = str_replace('{country.name}', config('country.name'), $keywords);
		
		if (empty($title)) {
			$title = data_get($page, 'title') . ' - ' . config('app.name');
		}
		if (empty($description)) {
			$description = str(str_strip(strip_tags(data_get($page, 'content'))))->limit(200);
		}
		
		$title = removeUnmatchedPatterns($title);
		$description = removeUnmatchedPatterns($description);
		$keywords = removeUnmatchedPatterns($keywords);
		
		MetaTag::set('title', $title);
		MetaTag::set('description', $description);
		MetaTag::set('keywords', $keywords);
		
		// Open Graph
		$this->og->title($title)->description($description);
		if (!empty(data_get($page, 'picture_url'))) {
			if ($this->og->has('image')) {
				$this->og->forget('image')->forget('image:width')->forget('image:height');
			}
			$this->og->image(data_get($page, 'picture_url'), [
				'width'  => 600,
				'height' => 600,
			]);
		}
		
		view()->share('og', $this->og);
		
		return appView('pages.cms', compact('page'));
	}
	
	/**
	 * @return \Illuminate\Contracts\View\View
	 * @throws \Exception
	 */
	public function contact()
	{
		// Get the Country's largest city for Google Maps
		$cacheId = config('country.code') . '.city.population.desc.first';
		$city = cache()->remember($cacheId, $this->cacheExpiration, function () {
			return City::currentCountry()->orderByDesc('population')->first();
		});
		
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('contact');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		return appView('pages.contact', compact('city'));
	}
	
	/**
	 * @param \App\Http\Requests\ContactRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function contactPost(ContactRequest $request)
	{
		// Add required data in the request for API
		request()->merge([
			'country_code' => config('country.code'),
			'country_name' => config('country.name'),
		]);
		
		// Call API endpoint
		$endpoint = '/contact';
		$data = makeApiRequest('post', $endpoint, $request->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			return back()->withErrors(['error' => $message])->withInput();
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
		} else {
			flash($message)->error();
		}
		
		return redirect(UrlGen::contact());
	}
}
