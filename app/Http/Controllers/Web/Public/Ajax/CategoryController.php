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

namespace App\Http\Controllers\Web\Public\Ajax;

use App\Http\Controllers\Web\Public\Post\CreateOrEdit\Traits\CategoriesTrait;
use App\Http\Controllers\Web\Public\FrontController;
use App\Models\Category;
use App\Models\SubCategoryGroup;
use Illuminate\Http\Request;

class CategoryController extends FrontController
{
	use CategoriesTrait;
	
	protected array $catsWithSubCatsTypes = ['cc_normal_list', 'cc_normal_list_s'];
	protected array $catsWithPictureTypes = ['c_picture_list', 'c_bigIcon_list'];
	protected string $catDisplayType = 'c_bigIcon_list';
	protected ?int $maxItems = null;
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCategoriesHtml(Request $request): \Illuminate\Http\JsonResponse
	{
		$languageCode = $request->input('languageCode', config('app.locale'));
		$selectedCatId = $request->input('selectedCatId');
		$catId = $request->input('catId');
		$catId = !empty($catId) ? $catId : null; // Change 0 to null
		$page = $request->integer('page');
		
		// Get Categories Options - Call API endpoint
		$categoriesOptions = $this->getCategoriesOptions();
		
		// Update global vars
		$this->catDisplayType = $categoriesOptions['cat_display_type'] ?? $this->catDisplayType;
		$this->cacheExpiration = isset($categoriesOptions['cache_expiration']) ? (int)$categoriesOptions['cache_expiration'] : $this->cacheExpiration;
		$this->maxItems = (isset($categoriesOptions['max_items'])) ? (int)$categoriesOptions['max_items'] : $this->maxItems;
		
		// Get category by ID - Call API endpoint
		$category = $this->getCategoryById($catId, $languageCode);
		
		// Get categories - Call API endpoint
		$apiMessage = null;
		$apiResult = $this->getCategories($catId, $languageCode, $apiMessage, $page);
		
		// Get categories list and format it
		$categories = data_get($apiResult, 'data', []);
		$formattedCats = $this->formatCategories($categories, $catId);
		
		$hasChildren = (
			empty($catId)
			|| (
				!empty($category)
				&& isset($category['children'])
				&& !empty($category['children'])
			)
		);
		
		$data = [
			'apiResult'         => $apiResult,
			'apiMessage'        => $apiMessage,
			'categoriesOptions' => $categoriesOptions,
			'categories'        => $formattedCats['categories'] ?? collect(),    // Adjacent Categories (Children)
			'subCategories'     => $formattedCats['subCategories'] ?? collect(), // Children of children
			'category'          => $category,
			'hasChildren'       => $hasChildren,
			'catId'             => $selectedCatId,
		];
		
		// Get categories list buffer
		$html = getViewContent('post.createOrEdit.inc.category.select', $data);
		
		// Send JSON Response
		$result = [
			'html'        => $html,
			'category'    => $category,
			'hasChildren' => $hasChildren,
			'parent'      => $category['parent'] ?? null,
		];
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getSubCategoryGroupsHtml(Request $request): \Illuminate\Http\JsonResponse
	{
		$subCategoryGroupId = $request->input('subCategoryGroupId');

		$Categories = SubCategoryGroup::find($subCategoryGroupId)->categories;

		$categoriesArray = json_decode(json_encode($Categories),true);

		foreach ($categoriesArray as $key => $value) {
			$categoriesArray[$key]['children'] = (Category::where('parent_id', $value['id'])->exists()) ? 1 : 0;
		}

		return response()->json($categoriesArray, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param $catId
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCustomFieldsHtml($catId, Request $request): \Illuminate\Http\JsonResponse
	{
		$languageCode = $request->input('languageCode');
		$postId = $request->input('postId');
		
		// Set the xhr data
		$xhrData = ['customFields' => ''];
		
		if (empty($catId)) {
			return response()->json($xhrData, 200, [], JSON_UNESCAPED_UNICODE);
		}
		
		// Get the Category's Custom Fields - Call API endpoint
		$endpoint = '/categories/' . $catId . '/fields';
		$queryParams = [
			'post_id'       => $postId,
			'errors'        => $request->input('errors'),
			'oldInput'      => $request->input('oldInput'),
			'sort'          => '-lft',
			'language_code' => $languageCode ?? config('app.locale'),
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('post', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		
		$fields = data_get($data, 'result');
		$errors = data_get($data, 'extra.errors');
		$oldInput = data_get($data, 'extra.oldInput');
		
		// Get the Custom Fields (in HTML)
		$customFields = '';
		if (!empty($fields)) {
			$data = [
				'fields'       => $fields,
				'languageCode' => $languageCode,
				'errors'       => $errors,
				'oldInput'     => $oldInput,
			];
			$customFields = getViewContent('post.createOrEdit.inc.fields', $data);
		}
		
		// Update the xhr data
		$xhrData['customFields'] = $customFields;
		
		return response()->json($xhrData, 200, [], JSON_UNESCAPED_UNICODE);
	}
}
