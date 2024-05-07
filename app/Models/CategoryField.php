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

namespace App\Models;

use App\Helpers\Arr;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

class CategoryField extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'category_field';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = false;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'category_id',
		'field_id',
		'disabled_in_subcategories',
		'parent_id',
		'lft',
		'rgt',
		'depth',
	];
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	// protected $casts = [];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
	}
	
	public function getCategoryHtml()
	{
		$out = '';
		if (!empty($this->category)) {
			$currentUrl = preg_replace('#/(search)$#', '', url()->current());
			$editUrl = $currentUrl . '/' . $this->category->id . '/edit';
			
			$out .= '<a href="' . $editUrl . '">' . $this->category->name . '</a>';
		} else {
			$out .= '--';
		}
		
		return $out;
	}
	
	public function getFieldHtml()
	{
		$out = '';
		if (!empty($this->field)) {
			$currentUrl = preg_replace('#/(search)$#', '', url()->current());
			// $editUrl = $currentUrl . '/' . $this->field->id . '/edit';
			$editUrl = $currentUrl . '/' . $this->id . '/edit';
			
			$out .= '<a href="' . $editUrl . '" style="float:left;">' . $this->field->name . '</a>';
			
			if (in_array($this->field->type, ['select', 'radio', 'checkbox_multiple'])) {
				$optionUrl = admin_url('custom_fields/' . $this->field->id . '/options');
				$out .= ' ';
				$out .= '<span style="float:right;">';
				$out .= '<a class="btn btn-xs btn-danger" href="' . $optionUrl . '"><i class="fa fa-cog"></i> ' . mb_ucfirst(trans('admin.options')) . '</a>';
				$out .= '</span>';
			}
		} else {
			$out .= '--';
		}
		
		return $out;
	}
	
	public function getDisabledInSubCategoriesHtml()
	{
		return checkboxDisplay($this->disabled_in_subcategories);
	}
	
	/**
	 * Get Fields details
	 *
	 * @param $catId
	 * @param null $postId
	 * @param null $languageCode (Required for AJAX Requests in v4.8 and lower)
	 * @return \Illuminate\Support\Collection
	 */
	public static function getFields($catId, $postId = null, $languageCode = null)
	{
		$fields = [];
		
		// Make sure that the category nested IDs variable are not empty
		if (empty($catId)) {
			return collect();
		}
		
		// Get Post's Custom Fields values
		$postFieldsValues = collect();
		if (!empty($postId) && trim($postId) != '') {
			$postFieldsValues = PostValue::where('post_id', $postId)->get();
			$postFieldsValues = self::keyingByFieldId($postFieldsValues);
		}
		
		$parentIds = Category::getParentsIds($catId);
		
		// Get Category's fields
		$catFields = self::with(['field', 'field.options']);
		if (!empty($parentIds)) {
			$catFields = $catFields->where(function ($query) use ($parentIds) {
				$i = 0;
				foreach ($parentIds as $parentId) {
					if ($i == 0) {
						$query->where(function ($q) use ($parentId) {
							$q->where('category_id', $parentId)->availableForSubCats();
						});
					} else {
						$query->orWhere(function ($q) use ($parentId) {
							$q->where('category_id', $parentId)->availableForSubCats();
						});
					}
					$i++;
				}
			});
			$catFields = $catFields->orWhere('category_id', $catId);
		} else {
			$catFields = $catFields->where('category_id', $catId);
		}
		$catFields = $catFields->orderBy('lft')->get();
		
		// Match Fields & Fields Values
		if ($catFields->count() > 0) {
			foreach ($catFields as $key => $catField) {
				if (!empty($catField->field)) {
					$fields[$key] = Arr::toObject($catField->field->toArray());
					
					if (isset($fields[$key]->options)) {
						$fields[$key]->options = collect($fields[$key]->options);
					}
					
					// Retrieve the Field's Default value
					if ($postFieldsValues->count() > 0) {
						if ($postFieldsValues->has($catField->field->id)) {
							$postValue = $postFieldsValues->get($catField->field->id);
							if (isset($postValue->value)) {
								$defaultValue = $postValue->value;
							} else {
								if ($catField->field->options->count() > 0) {
									$selectedOptions = [];
									foreach ($catField->field->options as $option) {
										if (isset($postValue[$option->id])) {
											$selectedOptions[$option->id] = $option;
										}
									}
									$defaultValue = $selectedOptions;
								} else {
									$defaultValue = [];
								}
							}
							
							$fields[$key]->default_value = $defaultValue;
						}
					}
					
				}
			}
		}
		
		return collect($fields);
	}
	
	/**
	 * @param $values
	 * @return \Illuminate\Support\Collection
	 */
	private static function keyingByFieldId($values): \Illuminate\Support\Collection
	{
		if (empty($values) || $values->count() <= 0) {
			return $values;
		}
		
		$postValues = [];
		foreach ($values as $value) {
			if (!empty($value->option_id)) {
				$postValues[$value->field_id][$value->option_id] = $value;
			} else {
				$postValues[$value->field_id] = $value;
			}
		}
		
		return collect($postValues);
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id');
	}
	
	public function field()
	{
		return $this->belongsTo(Field::class, 'field_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeAvailableForSubCats($builder)
	{
		return $builder->columnIsEmpty('disabled_in_subcategories');
	}
	
	public function scopeUnavailableForSubCats($builder)
	{
		return $builder->where('disabled_in_subcategories', 1);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
