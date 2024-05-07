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

namespace App\Http\Controllers\Web\Public\Ajax\Location;

use App\Http\Controllers\Web\Public\FrontController;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use App\Models\SubAdmin2;

class SelectController extends FrontController
{
	/**
	 * Form Select Box
	 * Get Countries
	 *
	 * @return mixed
	 */
	public function getCountries()
	{
		return $this->countries->toJson();
	}
	
	/**
	 * Form Select Box
	 * Get country Locations (admin1 OR admin2)
	 *
	 * @param $countryCode
	 * @param $adminType
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getAdmins($countryCode, $adminType): \Illuminate\Http\JsonResponse
	{
		$languageCode = request()->get('languageCode', config('app.locale'));
		$adminsEndpoints = [
			'1' => '/countries/' . $countryCode . '/subAdmins1',
			'2' => '/countries/' . $countryCode . '/subAdmins2',
		];
		
		// If admin type does not exist, set the default type
		if (!isset($adminsEndpoints[$adminType])) {
			$adminType = 1;
		}
		
		// Get country's admin. divisions - Call API endpoint
		$endpoint = $adminsEndpoints[$adminType];
		$queryParams = [
			'sort'          => '-name',
			'language_code' => $languageCode,
			'perPage'       => ($adminType == 2) ? 5000 : 200,
		];
		if ($adminType == 2) {
			$queryParams['embed'] = 'subAdmin1';
		}
		if (!empty($page)) {
			$queryParams['page'] = $page;
		}
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');
		$admins = data_get($apiResult, 'data');
		
		// No admin. division found. Display error.
		if (empty($admins)) {
			$message = $apiMessage ?? t('admin_division_does_not_exists', [], 'global', $languageCode);
			$result = ['message' => $message];
			
			return response()->json($result, 404);
		}
		
		// Get & formats the admin. divisions
		$adminsArr = [];
		foreach ($admins as $admin) {
			$name = data_get($admin, 'name');
			
			// Change the name for admin. division 2
			if ($adminType == 2) {
				$admin1Name = data_get($admin, 'subAdmin1.name');
				$name = !empty($admin1Name) ? $name . ', ' . $admin1Name : $name;
			}
			
			$adminsArr[] = [
				'code' => data_get($admin, 'code'),
				'name' => $name,
			];
		}
		
		return response()->json(['data' => $adminsArr], 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Form Select Box
	 * Get Admin1 or Admin2's Cities
	 *
	 * @param $countryCode
	 * @param $adminType
	 * @param $adminCode
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getCities($countryCode, $adminType, $adminCode): \Illuminate\Http\JsonResponse
	{
		$languageCode = request()->get('languageCode', config('app.locale'));
		$query = request()->get('q');
		$page = request()->integer('page', 1);
		$adminsTypes = ['1', '2'];
		
		$queryParams = [
			'q'             => $query,
			'autocomplete'  => 1,
			'sort'          => 'population',
			'language_code' => $languageCode,
			'perPage'       => 10,
		];
		if (!empty($page)) {
			$queryParams['page'] = $page;
		}
		$headers = [];
		
		if (!in_array($adminType, $adminsTypes) || $adminCode == '0') {
			$queryParams['embed'] = 'subAdmin1,subAdmin2';
			
			// Get country's cities - Call API endpoint
			$apiResult = $this->getCitiesFromApi($countryCode, $queryParams, $headers);
			$cities = data_get($apiResult, 'data');
		} else {
			$embedQsValue = 'subAdmin' . $adminType;
			$adminCodeQs = 'admin' . $adminType . 'Code';
			$queryParams['embed'] = $embedQsValue;
			$queryParams[$adminCodeQs] = $adminCode;
			$headers['X-WEB-REQUEST-URL'] = request()->fullUrlWithQuery([$adminCodeQs => $adminCode]);
			
			// Get country's cities - Call API endpoint
			$apiResult = $this->getCitiesFromApi($countryCode, $queryParams, $headers);
			$cities = data_get($apiResult, 'data');
			
			// If the admin. division's type is 2 and If no cities are found...
			// then, get cities from they admin. division 1
			if ($adminType == 2) {
				if (isset($queryParams[$adminCodeQs])) {
					unset($queryParams[$adminCodeQs]);
				}
				$queryParams['embed'] = 'subAdmin1';
				if (empty($cities)) {
					$queryParams['admin1Code'] = $adminCode;
					$headers['X-WEB-REQUEST-URL'] = request()->fullUrlWithQuery(['admin1Code' => $adminCode]);
					
					// Get country's cities - Call API endpoint
					$apiResult = $this->getCitiesFromApi($countryCode, $queryParams, $headers);
					$cities = data_get($apiResult, 'data');
				}
			}
		}
		$totalEntries = (int)data_get($apiResult, 'meta.total', 0);
		
		// Get Cities Array
		$items = [];
		if (!empty($cities)) {
			foreach ($cities as $city) {
				$cityName = data_get($city, 'name');
				$admin2Name = data_get($city, 'subAdmin2.name');
				$admin1Name = data_get($city, 'subAdmin1.name');
				
				$fullCityName = !empty($admin2Name)
					? $cityName . ', ' . $admin2Name
					: (!empty($admin1Name) ? $cityName . ', ' . $admin1Name : $cityName);
				
				$items[] = [
					'id'   => data_get($city, 'id'),
					'text' => $fullCityName,
				];
			}
		}
		
		return response()->json(['items' => $items, 'totalEntries' => $totalEntries], 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Form Select Box
	 * Get the selected City
	 *
	 * @param $countryCode
	 * @param $cityId
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getSelectedCity($countryCode, $cityId): \Illuminate\Http\JsonResponse
	{
		$languageCode = request()->get('languageCode', config('app.locale'));
		
		// Get the City by its ID - Call API endpoint
		$endpoint = '/cities/' . $cityId;
		$queryParams = [
			'embed'         => 'subAdmin1,subAdmin2',
			'language_code' => $languageCode,
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$city = data_get($data, 'result');
		
		if (empty($city)) {
			$item = [
				'id'   => 0,
				'text' => t('select_a_city', [], 'global', $languageCode),
			];
			
			return response()->json($item, 200, [], JSON_UNESCAPED_UNICODE);
		}
		
		$cityName = data_get($city, 'name');
		$admin2Name = data_get($city, 'subAdmin2.name');
		$admin1Name = data_get($city, 'subAdmin1.name');
		
		$fullCityName = !empty($admin2Name)
			? $cityName . ', ' . $admin2Name
			: (!empty($admin1Name) ? $cityName . ', ' . $admin1Name : $cityName);
		
		$item = [
			'id'   => data_get($city, 'id'),
			'text' => $fullCityName,
		];
		
		return response()->json($item, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param string $countryCode
	 * @param array $queryParams
	 * @param array $headers
	 * @return array
	 */
	private function getCitiesFromApi(string $countryCode, array $queryParams = [], array $headers = []): array
	{
		$endpoint = '/countries/' . $countryCode . '/cities';
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams, $headers);
		
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');
		
		return is_array($apiResult) ? $apiResult : [];
	}

	public function getCitesByDisID($id){
		
		
		$getdis = DB::table('subadmin2')->where("id", $id)->first();
		$tempArray = [];
		
		$getCites = DB::table('cities')->where("subadmin2_code", $getdis->code)->get();
		// $cities = data_get($getCites, 'data');
		// dd($cities);
		// $cityName = data_get($city, 'name');
		// $returnArray = [];
		// foreach($getCites as $getCity){
			// $getCity->temname = data_get($getCity, 'name');
			// dd($getCity);
			// $getCity->dis = $getdis->name;
			// array_push($tempArray, $getCity);
		// }
		
        // dd($tempArray);
		
		return json_encode([
			"data"=>$getCites,
			"dis"=>$getdis->name,
			"disID"=>$getdis->id,
			"disCode" => $getdis->code,
		]);
	}

	public function getSubcategoryBycatID($id){
		$category = DB::table('categories')->where("id", $id)->first();
		$subCategories = DB::table('categories')->where("parent_id", $id)->get()->toArray();
		
		// dd($subCategories);
		
		return json_encode([
			"data"=>$subCategories,
			"cat"=>$category
		]);
	}
}
