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

namespace App\Http\Controllers\Api\Post\Search;

use App\Http\Controllers\Api\Category\CategoryBySlug;
use App\Models\Category;

trait CategoryTrait
{
	use CategoryBySlug;
	
	/**
	 * Get Category (Auto-detecting ID or Slug)
	 *
	 * @return mixed|null
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function getCategory()
	{
		$cat = null;
		
		// Get the Category's right arguments
		$catParentId = null;
		$catId = null;
		if (request()->filled('c')) {
			$catId = request()->get('c');
			if (request()->filled('sc')) {
				$catParentId = $catId;
				$catId = request()->get('sc');
			}
		}
		
		$catParentId = (is_numeric($catParentId) || is_string($catParentId)) ? $catParentId : null;
		$catId = (is_numeric($catId) || is_string($catId)) ? $catId : null;
		
		// Get the Category
		if (!empty($catId)) {
			if (is_numeric($catId)) {
				$cat = $this->getCategoryById($catId);
			} else {
				if (empty($catParentId) || (!is_numeric($catParentId))) {
					$cat = $this->getCategoryBySlug($catId, $catParentId);
				}
			}
			
			if (empty($cat)) {
				abort(404, t('category_not_found'));
			}
		}
		
		return $cat;
	}
	
	/**
	 * Get Root Categories
	 *
	 * @return mixed
	 */
	public function getRootCategories()
	{
		$cacheId = 'cat.0.categories.' . config('app.locale');
		$cats = cache()->remember($cacheId, $this->cacheExpiration, function () {
			return Category::root()->orderBy('lft')->get();
		});
		
		if ($cats->count() > 0) {
			$cats = $cats->keyBy('id');
		}
		
		return $cats;
	}
}
