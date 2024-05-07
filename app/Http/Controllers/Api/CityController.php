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

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Http\Resources\EntityCollection;
use App\Http\Resources\CityResource;

/**
 * @group Countries
 */
class CityController extends BaseController
{
	/**
	 * List cities
	 *
	 * @queryParam embed string Comma-separated list of the city relationships for Eager Loading - Possible values: country,subAdmin1,subAdmin2. Example: null
	 * @queryParam sort string|array The sorting parameter (Order by DESC with the given column. Use "-" as prefix to order by ASC). Possible values: name,population. Example: -name
	 * @queryParam perPage int Items per page. Can be defined globally from the admin settings. Cannot be exceeded 100. Example: 2
	 * @queryParam page int Items page number. From 1 to ("total items" divided by "items per page value - perPage"). Example: 1
	 *
	 * @urlParam countryCode string The country code of the country of the cities to retrieve. Example: US
	 *
	 * @param $countryCode
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function index($countryCode): \Illuminate\Http\JsonResponse
	{
		$embed = explode(',', request()->get('embed'));
		$admin1Code = request()->get('admin1Code');
		$admin2Code = request()->get('admin2Code');
		$keyword = request()->get('q');
		$autocomplete = (request()->filled('autocomplete') && request()->integer('autocomplete') == 1);
		$locale = config('app.locale');
		$page = request()->integer('page');
		
		// Cache ID
		$cacheEmbedId = request()->filled('embed') ? '.embed.' . request()->get('embed') : '';
		$cacheFiltersId = '.filters.' . $admin1Code . $admin2Code . $keyword . (int)$autocomplete;
		$cachePageId = '.page.' . $page . '.of.' . $this->perPage;
		$cacheId = $countryCode . '.cities.' . $cacheEmbedId . $cacheFiltersId . $cachePageId . '.' . $locale;
		$cacheId = md5($cacheId);
		
		// Cached Query
		$cities = cache()->remember($cacheId, $this->cacheExpiration, function () use ($embed, $countryCode, $admin1Code, $admin2Code, $keyword, $autocomplete) {
			$cities = City::query();
			
			if (in_array('country', $embed)) {
				$cities->with('country');
			}
			if (in_array('subAdmin1', $embed)) {
				$cities->with('subAdmin1');
			}
			if (in_array('subAdmin2', $embed)) {
				$cities->with('subAdmin2');
			}
			
			$cities->where('country_code', $countryCode);
			if (!empty($admin1Code)) {
				$cities->where('subadmin1_code', $admin1Code);
			}
			if (!empty($admin2Code)) {
				$cities->where('subadmin2_code', $admin2Code);
			}
			if (!empty($keyword)) {
				if ($autocomplete) {
					$cities->transWhere('name', 'LIKE', $keyword . '%');
				} else {
					$cities->transWhere('name', 'LIKE', '%' . $keyword . '%');
				}
			}
			
			// Sorting
			$cities = $this->applySorting($cities, ['name', 'population']);
			
			return $cities->paginate($this->perPage);
		});
		
		// If the request is made from the app's Web environment,
		// use the Web URL as the pagination's base URL
		$cities = setPaginationBaseUrl($cities);
		
		$resourceCollection = new EntityCollection(class_basename($this), $cities);
		
		$message = ($cities->count() <= 0) ? t('no_cities_found') : null;
		
		return $this->respondWithCollection($resourceCollection, $message);
	}
	
	/**
	 * Get city
	 *
	 * @queryParam embed string Comma-separated list of the city relationships for Eager Loading - Possible values: country,subAdmin1,subAdmin2. Example: country
	 *
	 * @urlParam id int required The city's ID. Example: 12544
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function show($id): \Illuminate\Http\JsonResponse
	{
		$embed = explode(',', request()->get('embed'));
		
		// Cache ID
		$cacheEmbedId = request()->filled('embed') ? '.embed.' . request()->get('embed') : '';
		$cacheId = 'city.' . $id . $cacheEmbedId;
		
		// Cached Query
		$city = cache()->remember($cacheId, $this->cacheExpiration, function () use ($id, $embed) {
			$city = City::query()->where('id', $id);
			
			if (in_array('country', $embed)) {
				$city->with('country');
			}
			if (in_array('subAdmin1', $embed)) {
				$city->with('subAdmin1');
			}
			if (in_array('subAdmin2', $embed)) {
				$city->with('subAdmin2');
			}
			
			return $city->first();
		});
		
		abort_if(empty($city), 404, t('city_not_found'));
		
		$resource = new CityResource($city);
		
		return $this->respondWithResource($resource);
	}
}
