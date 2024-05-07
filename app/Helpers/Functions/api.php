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

/**
 * Check if the current request is from the API
 *
 * @return bool
 */
function isFromApi(): bool
{
	$isFromApi = false;
	
	if (
		request()->segment(1) == 'api'
		|| (request()->hasHeader('X-API-CALLED') && request()->header('X-API-CALLED'))
	) {
		$isFromApi = true;
	}
	
	return $isFromApi;
}

/**
 * Check if a request is made from the official web version of the app
 *
 * @return bool
 */
function isFromTheAppsWebEnvironment(): bool
{
	return (request()->hasHeader('X-AppType') && request()->header('X-AppType') == 'web');
}

/**
 * Make an API HTTP request
 *
 * @param string $method
 * @param string $uri
 * @param array $data
 * @param array $headers
 * @param bool $asMultipart
 * @param bool $forInternalEndpoint
 * @return array|mixed
 */
function makeApiRequest(string $method, string $uri, array $data = [], array $headers = [], bool $asMultipart = false, bool $forInternalEndpoint = true)
{
	try {
		/*
		 * Check if the endpoint is an external one
		 * i.e.The endpoint is a valid URL starting with 'http', except the website's URL
		 */
		$isRemoteEndpoint = (str_starts_with($uri, 'http') && !str_starts_with($uri, url('/')));
		
		if (!$isRemoteEndpoint) {
			$nonCacheableRequestMethods = ['POST', 'PUT', 'DELETE', 'PATCH', 'CREATE', 'UPDATE'];
			
			// Apply persistent (required) inputs for API calls
			$defaultData = [
				'countryCode'  => config('country.code'),
				'languageCode' => config('app.locale'),
			];
			if (in_array(request()->method(), $nonCacheableRequestMethods)) {
				$defaultData['country_code'] = (isset($data['country_code']) && !empty($data['country_code']))
					? $data['country_code']
					: config('country.code');
				$defaultData['language_code'] = (isset($data['language_code']) && !empty($data['language_code']))
					? $data['language_code']
					: config('app.locale');
			}
			$data = array_merge($defaultData, $data);
			
			// HTTP Client default headers for API calls
			$defaultHeaders = [
				'Content-Language'  => $defaultData['languageCode'] ?? null,
				'Accept'            => 'application/json',
				'X-AppType'         => 'web',
				'X-CSRF-TOKEN'      => csrf_token(),
				'X-WEB-REQUEST-URL' => request()->url(),
			];
			$appApiToken = config('larapen.core.api.token');
			if (!empty($appApiToken)) {
				$defaultHeaders['X-AppApiToken'] = $appApiToken;
			}
			if (session()->has('authToken')) {
				$defaultHeaders['Authorization'] = 'Bearer ' . session()->get('authToken');
			}
			
			// Prevent HTTP request caching for methods that can update the database
			if (in_array(strtoupper($method), $nonCacheableRequestMethods)) {
				$noCacheHeaders = config('larapen.core.noCacheHeaders');
				if (!empty($noCacheHeaders)) {
					foreach ($noCacheHeaders as $key => $value) {
						$defaultHeaders[$key] = $value;
					}
				}
			}
			$headers = array_merge($defaultHeaders, $headers);
		}
		
		if (strtolower(config('larapen.core.api.client')) == 'curl' || $isRemoteEndpoint) {
			$array = curlHttpRequest($method, $uri, $data, $headers, $asMultipart, $forInternalEndpoint);
		} else {
			$array = laravelSubRequest($method, $uri, $data, $headers, $asMultipart, $forInternalEndpoint);
		}
	} catch (\Throwable $e) {
		$message = $e->getMessage();
		if (empty($message)) {
			$message = 'Error encountered during API request.';
		}
		$array = [
			'success'      => false,
			'message'      => $message,
			'result'       => null,
			'isSuccessful' => false,
			'status'       => 500,
		];
	}
	
	return $array;
}

/**
 * Make an API HTTP request internally (using Laravel sub requests)
 *
 * NOTE: By sending a sub request within the application,
 * you can simply consume your applications API without having to send separated, slower HTTP requests.
 *
 * @param string $method
 * @param string $uri
 * @param array $data
 * @param array $headers
 * @param bool $asMultipart
 * @param bool $forInternalEndpoint
 * @return array|mixed
 */
function laravelSubRequest(string $method, string $uri, array $data = [], array $headers = [], bool $asMultipart = false, bool $forInternalEndpoint = true)
{
	$baseUrl = '/api';
	$endpoint = $forInternalEndpoint ? ($baseUrl . $uri) : $uri;
	
	// Store the original request headers and input data
	config()->set('request.original.headers', request()->headers->all());
	// $originalRequest = request()->all();
	$originalRequest = request()->input();
	request()->merge($data);
	
	try {
		
		// Request segments are not available when making sub requests,
		// The 'X-API-CALLED' header is set for the function isFromApi()
		$localHeaders = [
			'X-API-CALLED' => true,
		];
		$headers = array_merge($headers, $localHeaders);
		
		// Create the request to the internal API
		$cookies = [];
		$request = request()->create($endpoint, strtoupper($method), $data, $cookies, request()->file());
		
		// Apply the available headers to the request
		if (!empty($headers)) {
			foreach ($headers as $key => $value) {
				request()->headers->set($key, $value);
			}
		}
		
		// Dispatch the request instance with the router
		// NOTE: If you're consuming your own API,
		// use app()->handle() instead of \Route::dispatch()
		// $response = app()->handle($request);
		$response = \Route::dispatch($request);
		
		// Fetch the response
		// dd($response->getData());
		$json = $response->getContent();
		
		// dd($json); // Debug!
		$array = json_decode($json, true);
		
		$array['isSuccessful'] = $response->isSuccessful();
		$array['status'] = $response->status();
		
	} catch (\Throwable $e) {
		$message = $e->getMessage();
		if (empty($message)) {
			$message = 'Error encountered during API request.';
		}
		$array = [
			'success'      => false,
			'message'      => $message,
			'result'       => null,
			'isSuccessful' => false,
			'status'       => 500,
		];
	}
	
	// Restore the request headers & input back to the original state
	if (config('request.original.headers')) {
		request()->headers->replace(config('request.original.headers'));
	}
	request()->replace($originalRequest);
	
	return $array;
}

/**
 * Make an API HTTP request remotely (using CURL)
 *
 * @param string $method
 * @param string $uri
 * @param array $data
 * @param array $headers
 * @param bool $asMultipart
 * @param bool $forInternalEndpoint
 * @return array|mixed
 */
function curlHttpRequest(string $method, string $uri, array $data = [], array $headers = [], bool $asMultipart = false, bool $forInternalEndpoint = true)
{
	$options = [
		'verify'  => false,
		'debug'   => false,
		'timeout' => config('larapen.core.api.timeout', 60),
	];
	
	$baseUrl = url('api');
	$endpoint = $forInternalEndpoint ? ($baseUrl . $uri) : $uri;
	
	try {
		
		$client = \Illuminate\Support\Facades\Http::withOptions($options);
		
		if (!empty($headers)) {
			$client->withHeaders($headers);
		}
		if ($asMultipart) {
			$client->asMultipart();
			$data = multipartFormData($data);
			$method = 'post';
		}
		if (strtolower($method) == 'get') {
			$response = $client->get($endpoint, $data);
		} else if (strtolower($method) == 'post') {
			$response = $client->post($endpoint, $data);
		} else if (strtolower($method) == 'put') {
			$response = $client->put($endpoint, $data);
		} else if (strtolower($method) == 'delete') {
			$response = $client->delete($endpoint, $data);
		} else {
			$options = [];
			if (!empty($data)) {
				$options = ['multipart' => $data];
			}
			$response = $client->send($method, $endpoint, $options);
		}
		$array = $response->json();
		
		$array['isSuccessful'] = $response->successful();
		$array['status'] = $response->status();
		
	} catch (\Throwable $e) {
		$array = [
			'success'      => false,
			'message'      => $e->getMessage(),
			'result'       => null,
			'isSuccessful' => false,
			'status'       => 500,
		];
	}
	
	return $array;
}

/**
 * Convert POST request to Guzzle multipart array format
 *
 * @param $inputs
 * @return array
 */
function multipartFormData($inputs): array
{
	$formData = [];
	
	$inputs = \App\Helpers\Arr::flattenPost($inputs);
	
	if (is_array($inputs) && !empty($inputs)) {
		foreach ($inputs as $key => $value) {
			if ($value instanceof \Illuminate\Http\UploadedFile) {
				$formData[] = [
					'name'     => $key,
					'contents' => fopen($value->getPathname(), 'r'),
					'filename' => $value->getClientOriginalName(),
				];
			} else {
				$formData[] = [
					'name'     => $key,
					'contents' => $value,
				];
			}
		}
	}
	
	return $formData;
}

/**
 * @return string|string[]|null
 */
function getApiAuthToken()
{
	$token = null;
	
	if (request()->hasHeader('Authorization')) {
		$authorization = request()->header('Authorization');
		if (str_contains($authorization, 'Bearer')) {
			$token = str_replace('Bearer ', '', $authorization);
		}
	}
	
	return $token;
}

/**
 * @param $paginatedCollection
 * @return mixed
 */
function setPaginationBaseUrl($paginatedCollection)
{
	// If the request is made from the app's Web environment,
	// use the Web URL as the pagination's base URL
	if (isFromTheAppsWebEnvironment()) {
		if (request()->hasHeader('X-WEB-REQUEST-URL')) {
			if (method_exists($paginatedCollection, 'setPath')) {
				$paginatedCollection->setPath(request()->header('X-WEB-REQUEST-URL'));
			}
		}
	}
	
	return $paginatedCollection;
}

/**
 * @return bool
 */
function isPostCreationRequest(): bool
{
	if (isFromApi()) {
		$isPostCreationRequest = (str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'Api\PostController@store'));
	} else {
		$isNewEntryUri = (
			(
				config('settings.single.publication_form_type') == '1'
				&& request()->segment(2) == 'create'
			)
			|| (
				config('settings.single.publication_form_type') == '2'
				&& request()->segment(1) == 'create'
			)
		);
		
		$isPostCreationRequest = (
			$isNewEntryUri
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'Post\CreateOrEdit\MultiSteps\CreateController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'Post\CreateOrEdit\SingleStep\CreateController')
		);
	}
	
	return $isPostCreationRequest;
}
