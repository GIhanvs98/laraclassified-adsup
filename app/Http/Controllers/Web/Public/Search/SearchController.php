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

namespace App\Http\Controllers\Web\Public\Search;

use Illuminate\Support\Facades\Route;
use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Models\SubAdmin2;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SearchController extends BaseController
{
	/**
	 * @return \Illuminate\Contracts\View\View
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function index()
	{

		$allowedFilters = ['search', 'premium'];
		
		// Get the listings type parameter
		$filterBy = request()->get('filterBy', 'search');
		if (!in_array($filterBy, $allowedFilters)) {
			abort(403, t('unauthorized_filter'));
		}
		
		// Call API endpoints
		$endpoint = '/posts';
		$queryParams = [
			'op' => $filterBy,
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$headers = [
			'X-WEB-CONTROLLER' => class_basename(get_class($this)),
		];
		$data = makeApiRequest('get', $endpoint, $queryParams, $headers);
		// dd($data);
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');
		$apiExtra = data_get($data, 'extra');
		$preSearch = data_get($apiExtra, 'preSearch');

		/* codes from kenura to add memberships
		$requestMembers = "all";

		if(isset(request()->members)){

			if(request()->members == "only"){
						
				$requestMembers = "only";

				$users = User::with("membership")->get();
				
				foreach($apiResult as $resultKey => $posts){

					foreach ($posts as $postKey => $post) {
						
						foreach($users as $user){
							try {
									
								if($post["user_id"] == $user->id){

									if(isset($user->membership->id) and ($user->membership->id == 1 or $user->membership->id == "Non Member")){

										// echo "User do not have membership.<br>";
										$apiResult[$resultKey][$postKey] = [];
									}
								}
							} catch (\Throwable $th) {
								//throw $th;
							}
						}
					}
				}
			}
		}
		*/

		// Sidebar
		$this->bindSidebarVariables((array)data_get($apiExtra, 'sidebar'));
		
		// Get Titles
		$this->getHtmlTitle($preSearch);
		$this->getBreadcrumb($preSearch);
		
		// Meta Tags
		[$title, $description, $keywords] = $this->getMetaTag($preSearch);
		MetaTag::set('title', $title);
		MetaTag::set('description', $description);
		MetaTag::set('keywords', $keywords);
		
		// Open Graph
		$this->og->title($title)->description($description)->type('website');
		view()->share('og', $this->og);
		
		// SEO: noindex
		// Categories' Listings Pages
		$noIndexCategoriesQueryStringPages = (
			config('settings.seo.no_index_categories_qs')
			&& (
				!(config('larapen.core.api.client') === 'curl')
				|| str_contains(Route::currentRouteAction(), 'Search\SearchController')
			)
			&& !empty(data_get($preSearch, 'cat'))
		);
		
		// Cities' Listings Pages
		$noIndexCitiesQueryStringPages = (
			config('settings.seo.no_index_cities_qs')
			&& (
				!(config('larapen.core.api.client') === 'curl')
				|| str_contains(Route::currentRouteAction(), 'Search\SearchController')
			)
			&& !empty(data_get($preSearch, 'city'))
		);
		// Filters (and Orders) on Listings Pages (Except Pagination)
		$noIndexFiltersOnEntriesPages = (
			config('settings.seo.no_index_filters_orders')
			&& (
				!(config('larapen.core.api.client') === 'curl')
				|| str_contains(Route::currentRouteAction(), 'Search\\')
			)
			&& !empty(request()->except(['page']))
		);
		// "No result" Pages (Empty Searches Results Pages)
		$noIndexNoResultPages = (
			config('settings.seo.no_index_no_result')
			&& (
				!(config('larapen.core.api.client') === 'curl')
				|| str_contains(Route::currentRouteAction(), 'Search\\')
			)
			&& empty(data_get($apiResult, 'data'))
		);

		

		$alldis= SubAdmin2::get()->toArray();
		
		$tempArray = [];
		foreach($alldis as $alldi){
         $new = DB::table('subadmin2')->where("code", $alldi['code'])->first();
		 $alldi['originalID'] = $new->id;
		 array_push($tempArray, $alldi);
		}
		$tempAArray = [];
		$topPackge = DB::table('packages')->where("packge_type", "Top ads")->where("active", 1)->orderByRaw("RAND()")->first();
        foreach($apiResult as $posts){
			// dd($post);
			foreach($posts as $post){

				// dd($post['latestPayment'][0]['package'][0]['packge_type']);
				if(data_get($post, 'latestPayment.package.packge_type') === "Top ads"){
					// if($post['latestPayment'][0]['package'][0]['packge_type'] = "Top ads"){
						// dd($post);
						array_push($tempAArray, $post);
						// array_push($tempAArray, $posts[$key]);
					// }
				}else{

				}
			}
			
			
		}
		
		// dd($tempAArray);
		// $bumpPackge = DB::table('packages')->where("packge_type", "Bump Ads")->where("active", 1)->get()->toArray();

		//return $members." - ".print_r($apiResult);

		return appView(
			'search.results',
			compact(
				'apiMessage',
				'apiResult',
				'apiExtra',
				'noIndexCategoriesQueryStringPages',
				'noIndexCitiesQueryStringPages',
				'noIndexFiltersOnEntriesPages',
				'noIndexNoResultPages'
			),
			[
				'alldis' => $tempArray,
			    'topAds' => $tempAArray,
			]
		);
	}
}
