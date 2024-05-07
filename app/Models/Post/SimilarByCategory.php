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

namespace App\Models\Post;

use App\Models\Category;
use App\Models\Post;

trait SimilarByCategory
{
	/**
	 * Get similar Posts (Posts in the same Category)
	 *
	 * @param int|null $limit
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function getSimilarByCategory(?int $limit = 20)
	{
		$posts = Post::query();
		
		$postsTable = (new Post())->getTable();
		
		$select = [
			$postsTable . '.id',
			$postsTable . '.country_code',
			'category_id',
			'title',
			$postsTable . '.price',
			'city_id',
			'featured',
			'email_verified_at',
			'phone_verified_at',
			'reviewed_at',
			$postsTable . '.created_at',
			$postsTable . '.archived_at',
		];
		if (isFromApi() && !isFromTheAppsWebEnvironment()) {
			$select[] = $postsTable . '.description';
			$select[] = 'user_id';
			$select[] = 'contact_name';
			$select[] = $postsTable . '.auth_field';
			$select[] = $postsTable . '.phone';
			$select[] = $postsTable . '.email';
		}
		if (config('plugins.reviews.installed')) {
			$select[] = 'rating_cache';
			$select[] = 'rating_count';
		}
		
		if (!empty($select)) {
			foreach ($select as $column) {
				$posts->addSelect($column);
			}
		}
		
		// Get the sub-categories of the current listing parent's category
		$similarCatIds = [];
		if (!empty($this->category)) {
			if ($this->category->id == $this->category->parent_id) {
				$similarCatIds[] = $this->category->id;
			} else {
				if (!empty($this->category->parent_id)) {
					$similarCatIds = Category::childrenOf($this->category->parent_id)->get()
						->keyBy('id')
						->keys()
						->toArray();
					$similarCatIds[] = (int)$this->category->parent_id;
				} else {
					$similarCatIds[] = (int)$this->category->id;
				}
			}
		}
		
		// Default Filters
		$posts->currentCountry()->verified()->unarchived();
		if (config('settings.single.listings_review_activation')) {
			$posts->reviewed();
		}
		
		// Get listings from same category
		if (!empty($similarCatIds)) {
			if (count($similarCatIds) == 1) {
				if (isset($similarCatIds[0]) && !empty(isset($similarCatIds[0]))) {
					$posts->where('category_id', (int)$similarCatIds[0]);
				}
			} else {
				$posts->whereIn('category_id', $similarCatIds);
			}
		}
		
		// Relations
		$posts->with('postType');
		$posts->with('category', fn ($query) => $query->with('parent'))->has('category');
		$posts->with('pictures');
		$posts->with('city')->has('city');
		$posts->with('savedByLoggedUser');
		$posts->with('latestPayment', fn ($query) => $query->with('package'));
		$posts->with('user');
		$posts->with('user.permissions');
		
		if (isset($this->id)) {
			$posts->where($postsTable . '.id', '!=', $this->id);
		}
		
		// Set ORDER BY
		// $posts->orderBy('created_at', 'DESC');
		$seed = rand(1, 9999);
		$posts->inRandomOrder($seed);
		
		// return $posts->take((int)$limit)->get();
		return $posts->paginate((int)$limit);
	}
}
