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

namespace App\Http\Controllers\Api\Post;

use App\Helpers\Search\PostQueries;
use App\Http\Controllers\Api\Post\Search\CategoryTrait;
use App\Http\Controllers\Api\Post\Search\LocationTrait;
use App\Http\Controllers\Api\Post\Search\SidebarTrait;
use App\Http\Resources\EntityCollection;
use App\Http\Resources\PostResource;
use App\Models\CategoryField;
use App\Models\Post;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use Larapen\LaravelDistance\Libraries\mysql\DistanceHelper;

trait SearchTrait
{
	use CategoryTrait, LocationTrait, SidebarTrait;
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getPosts(): \Illuminate\Http\JsonResponse
	{
		// Create the MySQL Distance Calculation function, If it doesn't exist
		$distanceCalculationFormula = config('settings.list.distance_calculation_formula', 'haversine');
		if (!DistanceHelper::checkIfDistanceCalculationFunctionExists($distanceCalculationFormula)) {
			DistanceHelper::createDistanceCalculationFunction($distanceCalculationFormula);
		}
		
		$preSearch = [];
		$fields = collect();
		
		// Advanced Query (Query with the 'op' parameter)
		$options = ['search', 'premium', 'latest', 'free', 'premiumFirst'];
		
		$op = request()->get('op');
		if (in_array($op, $options) || $op == 'similar') {
			$embed = ['user', 'category', 'parent', 'postType', 'city', 'savedByLoggedUser', 'pictures', 'latestPayment', 'package'];
			request()->query->add(['embed' => implode(',', $embed)]);
			
			if (in_array($op, $options)) {
				$orderBy = request()->get('orderBy');
				$orderBy = ($orderBy != 'random') ? $orderBy : null;
				
				$input = [
					'op'      => $op,
					'perPage' => request()->get('perPage'),
					'orderBy' => $orderBy,
				];
				
				$searchData = $this->searchPosts($input, $preSearch, $fields);
				$preSearch = $searchData['preSearch'] ?? $preSearch;
				
				$data = [
					'success' => true,
					'message' => $searchData['message'] ?? null,
					'result'  => $searchData['posts'],
					'extra'   => [
						'count'     => $searchData['count'] ?? [],
						'preSearch' => $preSearch,
						'sidebar'   => $this->getSidebar($preSearch, $fields->toArray()),
						'tags'      => $searchData['tags'] ?? [],
					],
				];
				
				return $this->apiResponse($data);
			}
			
			// similar
			$postId = request()->get('postId');
			$posts = collect();
			if (!empty($postId)) {
				$distance = request()->get('distance');
				$res = $this->similarPosts($postId, $distance);
				$posts = $res['posts'] ?? collect();
				$post = $res['post'] ?? null;
				
				$postResource = new PostResource($post);
				$postApiResult = $this->respondWithResource($postResource)->getData(true);
				$post = data_get($postApiResult, 'result');
			}
			
			$resourceCollection = new EntityCollection(class_basename($this), $posts);
			$postsResult = $resourceCollection->toResponse(request())->getData(true);
			
			$totalPosts = $posts->count();
			$message = ($totalPosts <= 0) ? t('no_posts_found') : null;
			
			$data = [
				'success' => true,
				'message' => $message,
				'result'  => $postsResult, // $resourceCollection,
				'extra'   => [
					'count' => [$totalPosts],
				],
			];
			if (!empty($post)) {
				$data['extra']['preSearch'] = ['post' => $post];
			}
			
			return $this->apiResponse($data);
		}
		
		// Normal API Query (Search without the 'op' parameter)
		$posts = $this->normalQuery();
		
		$resourceCollection = new EntityCollection(class_basename($this), $posts);
		$message = ($posts->count() <= 0) ? t('no_posts_found') : null;
		$resourceCollection = $this->respondWithCollection($resourceCollection, $message);
		
		$data = json_decode($resourceCollection->content(), true);
		$data['extra'] = [
			'count'     => $count ?? null,
			'preSearch' => $preSearch,
			'fields'    => $fields,
		];
		
		return $this->apiResponse($data);
	}
	
	/**
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	protected function normalQuery()
	{
		$countryCode = request()->get('country_code', config('country.code'));
		$areBelongLoggedUser = (
			(request()->filled('belongLoggedUser') && request()->integer('belongLoggedUser') == 1)
			|| request()->get('logged')
		);
		$arePendingApproval = (request()->filled('pendingApproval') && request()->integer('pendingApproval') == 1);
		$areArchived = (request()->filled('archived') && request()->integer('archived') == 1);
		
		$posts = Post::query()->with(['user'])->whereHas('country')->countryOf($countryCode);
		
		if ($areBelongLoggedUser) {
			if (auth('sanctum')->check()) {
				$user = auth('sanctum')->user();
				
				$posts->where('user_id', $user->getAuthIdentifier());
				
				if ($arePendingApproval) {
					$posts->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->unverified();
				} else if ($areArchived) {
					$posts->archived();
				} else {
					$posts->verified()->unarchived()->reviewed();
				}
			} else {
				abort(401);
			}
		}
		
		$embed = explode(',', request()->get('embed'));
		
		if (in_array('country', $embed)) {
			$posts->with('country');
		}
		if (in_array('user', $embed)) {
			$posts->with('user');
		}
		if (in_array('category', $embed)) {
			$posts->with('category');
		}
		if (in_array('postType', $embed)) {
			$posts->with('postType');
		}
		if (in_array('city', $embed)) {
			$posts->with('city');
		}
		if (in_array('pictures', $embed)) {
			$posts->with('pictures');
		}
		if (in_array('latestPayment', $embed)) {
			if (in_array('package', $embed)) {
				$posts->with(['latestPayment' => fn($builder) => $builder->with(['package'])]);
			} else {
				$posts->with('latestPayment');
			}
		}
		
		// Sorting
		$posts = $this->applySorting($posts, ['created_at']);
		
		$posts = $posts->paginate($this->perPage);
		
		// If the request is made from the app's Web environment,
		// use the Web URL as the pagination's base URL
		$posts = setPaginationBaseUrl($posts);
		
		return $posts;
	}
	
	/**
	 * @param $input
	 * @param $preSearch
	 * @param $fields
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	protected function searchPosts($input, &$preSearch, &$fields): array
	{
		$location = $this->getLocation();
		
		$preSearch = [
			'cat'   => $this->getCategory(),
			'city'  => $location['city'] ?? null,
			'admin' => $location['admin'] ?? null,
		];
		
		if (!empty($preSearch['cat'])) {
			$fields = CategoryField::getFields($preSearch['cat']->id);
		}
		
		$queriesToRemove = ['op', 'embed'];
		
		return (new PostQueries($input, $preSearch))->fetch($queriesToRemove);
	}
	
	/**
	 * @param int|null $postId
	 * @param int|null $distance
	 * @return array
	 */
	protected function similarPosts(?int $postId, ?int $distance = 50): array
	{
		$posts = [];
		
		$cacheId = 'post.withoutGlobalScopes.' . $postId . '.' . config('app.locale');
		$post = cache()->remember($cacheId, $this->cacheExpiration, function () use ($postId) {
			return Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->with(['category', 'city'])
				->where('id', $postId)
				->first();
		});
		
		if ($post->count() <= 0) {
			return $posts;
		}
		
		$similarPostsLimit = (int)config('settings.single.similar_listings_limit', 10);
		if (config('settings.single.similar_listings') == '1') {
			$cacheId = 'posts.similar.category.' . $post->category_id . '.post.' . $post->id . '.limit.' . $similarPostsLimit;
			$posts = cache()->remember($cacheId, $this->cacheExpiration, function () use ($post, $similarPostsLimit) {
				return $post->getSimilarByCategory($similarPostsLimit);
			});
		}
		
		if (config('settings.single.similar_listings') == '2') {
			$distance = $distance ?? 50; // km OR miles
			$cacheId = 'posts.similar.city.' . $post->city_id . '.post.' . $post->id . '.limit.' . $similarPostsLimit;
			$posts = cache()->remember($cacheId, $this->cacheExpiration, function () use ($post, $distance, $similarPostsLimit) {
				return $post->getSimilarByLocation($distance, $similarPostsLimit);
			});
		}
		
		return ['post' => $post, 'posts' => $posts];
	}
}
