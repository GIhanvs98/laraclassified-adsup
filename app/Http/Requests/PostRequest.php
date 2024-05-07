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

namespace App\Http\Requests;

use App\Helpers\Number;
use App\Helpers\RemoveFromString;
use App\Models\Category;
use App\Models\Package;
use App\Models\Picture;
use App\Rules\BetweenRule;
use App\Rules\BlacklistTitleRule;
use App\Rules\BlacklistWordRule;
use App\Rules\MbAlphanumericRule;
use App\Rules\SluggableRule;
use App\Rules\UniquenessOfPostRule;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class PostRequest extends Request
{
	public static $packages;
	public static $paymentMethods;
	
	protected $cfMessages = [];
	
	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		// Don't apply this to the Admin Panel
		if (isAdminPanel()) {
			return;
		}
		
		$input = $this->all();
		
		// title
		if ($this->filled('title')) {
			$input['title'] = $this->input('title');
			$input['title'] = strCleanerLite($input['title']);
			$input['title'] = onlyNumCleaner($input['title']);
			$input['title'] = RemoveFromString::contactInfo($input['title'], true);
		}
		
		// description
		if ($this->filled('description')) {
			$input['description'] = $this->input('description');
			$input['description'] = onlyNumCleaner($input['description']);
			if (config('settings.single.wysiwyg_editor') != 'none') {
				try {
					$input['description'] = Purifier::clean($input['description']);
				} catch (\Throwable $e) {
				}
			} else {
				$input['description'] = strCleaner($input['description']);
			}
			$input['description'] = RemoveFromString::contactInfo($input['description'], true);
		}
		
		// price
		if ($this->has('price')) {
			if ($this->filled('price')) {
				$input['price'] = $this->input('price');
				// If field's value contains only numbers and dot,
				// Then decimal separator is set as dot.
				if (preg_match('/^[\d.]*$/', $input['price'])) {
					$input['price'] = Number::formatForDb($input['price'], '.');
				} else {
					if ($this->filled('currency_decimal_separator')) {
						$input['price'] = Number::formatForDb($input['price'], $this->input('currency_decimal_separator'));
					} else {
						$input['price'] = Number::formatForDb($input['price'], config('currency.decimal_separator', '.'));
					}
				}
			} else {
				$input['price'] = null;
			}
		}
		
		// currency_code
		if ($this->filled('currency_code')) {
			$input['currency_code'] = $this->input('currency_code');
		} else {
			$input['currency_code'] = config('currency.code', 'USD');
		}
		
		// contact_name
		if ($this->filled('contact_name')) {
			$input['contact_name'] = strCleanerLite($this->input('contact_name'));
			$input['contact_name'] = onlyNumCleaner($input['contact_name']);
		}
		
		// auth_field
		$input['auth_field'] = getAuthField();
		
		// phone
		if ($this->filled('phone')) {
			$input['phone'] = phoneE164($this->input('phone'), getPhoneCountry());
			$input['phone_national'] = phoneNational($this->input('phone'), getPhoneCountry());
		} else {
			$input['phone'] = null;
			$input['phone_national'] = null;
		}
		
		// tags
		if ($this->filled('tags')) {
			$input['tags'] = tagCleaner($this->input('tags'));
		}
		
		// is_permanent
		if ($this->filled('is_permanent')) {
			$input['is_permanent'] = $this->input('is_permanent');
			// For security purpose
			if (config('settings.single.permanent_listings_enabled') == '0') {
				$input['is_permanent'] = 0;
			} else {
				if (config('settings.single.permanent_listings_enabled') == '1' && $this->input('post_type_id') != 1) {
					$input['is_permanent'] = 0;
				}
				if (config('settings.single.permanent_listings_enabled') == '2' && $this->input('post_type_id') != 2) {
					$input['is_permanent'] = 0;
				}
				if (config('settings.single.permanent_listings_enabled') == '3' && $this->input('post_type_id') == 2) {
					$input['is_permanent'] = 1;
				}
			}
		} else {
			$input['is_permanent'] = 0;
		}
		
		request()->merge($input); // Required!
		$this->merge($input);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$guard = isFromApi() ? 'sanctum' : null;
		$authFields = array_keys(getAuthFields());
		
		$rules = [];
		
		$rules['category_id'] = ['required', 'not_in:0'];
		if (config('settings.single.show_listing_types')) {
			$rules['post_type_id'] = ['required', 'not_in:0'];
		}
		$rules['title'] = [
			'required',
			new BetweenRule(
				(int)config('settings.single.title_min_length', 2),
				(int)config('settings.single.title_max_length', 150)
			),
			new MbAlphanumericRule(),
			new SluggableRule(),
			new BlacklistTitleRule(),
		];
		if (config('settings.single.enable_post_uniqueness')) {
			$rules['title'][] = new UniquenessOfPostRule();
		}
		$rules['description'] = [
			'required',
			new BetweenRule(
				(int)config('settings.single.description_min_length', 5),
				(int)config('settings.single.description_max_length', 6000)
			),
			new MbAlphanumericRule(),
			new BlacklistWordRule(),
		];
		if (config('settings.single.price_mandatory') == '1') {
			if ($this->filled('category_id')) {
				$category = Category::find($this->input('category_id'));
				if (!empty($category)) {
					if ($category->type != 'not-salable') {
						$rules['price'] = ['required', 'numeric', 'gt:0'];
					}
				}
			}
		}
		$rules['contact_name'] = ['required', new BetweenRule(2, 200)];
		$rules['auth_field'] = ['required', Rule::in($authFields)];
		$rules['phone'] = ['max:30'];
		$rules['phone_country'] = ['required_with:phone'];
		$rules['city_id'] = ['required', 'not_in:0'];
		
		
		if (!auth($guard)->check()) {
			$rules['accept_terms'] = ['accepted'];
		}
		
		// CREATE
		if (in_array($this->method(), ['POST', 'CREATE'])) {
			// Apply this rules for the 'Single Step Form' (Web & API requests)
			// Or for API requests whatever the form type (ie. Single or Multi Steps)
			if (config('settings.single.publication_form_type') == '2' || isFromApi()) {
				// Pictures
				if ($this->file('pictures')) {
					$files = $this->file('pictures');
					foreach ($files as $key => $file) {
						if (!empty($file)) {
							$rules['pictures.' . $key] = [
								'image',
								'mimes:' . getUploadFileTypes('image'),
								'min:' . (int)config('settings.upload.min_image_size', 0),
								'max:' . (int)config('settings.upload.max_image_size', 1000),
							];
						}
					}
				} else {
					if (config('settings.single.picture_mandatory')) {
						$rules['pictures'] = ['required'];
					}
				}
			}
			
			// Single Step Form
			if (config('settings.single.publication_form_type') == '2') {
				// Package & PaymentMethod
				if (
					isset(self::$packages, self::$paymentMethods)
					&& self::$packages->count() > 0
					&& self::$paymentMethods->count() > 0
				) {
					// Require 'package_id' if Packages are available
					$rules['package_id'] = ['required'];
					
					// Require 'payment_method_id' if the selected package's price > 0
					if ($this->has('package_id')) {
						$package = Package::find($this->input('package_id'));
						if (!empty($package) && $package->price > 0) {
							$rules['payment_method_id'] = ['required', 'not_in:0'];
						}
					}
				}
			}
			
			$rules = $this->captchaRules($rules);
		}
		
		// UPDATE
		if (in_array($this->method(), ['PUT', 'PATCH', 'UPDATE'])) {
			// Single Step Form
			if (config('settings.single.publication_form_type') == '2') {
				// Pictures
				if ($this->file('pictures')) {
					$files = $this->file('pictures');
					foreach ($files as $key => $file) {
						if (!empty($file)) {
							$rules['pictures.' . $key] = [
								'image',
								'mimes:' . getUploadFileTypes('image'),
								'min:' . (int)config('settings.upload.min_image_size', 0),
								'max:' . (int)config('settings.upload.max_image_size', 1000),
							];
						}
					}
				} else {
					if (config('settings.single.picture_mandatory')) {
						$countPictures = Picture::where('post_id', $this->input('post_id'))->count();
						if ($countPictures <= 0) {
							$rules['pictures'] = ['required'];
						}
					}
				}
			}
		}
		
		// COMMON
		
		// Location
		if (config('settings.single.city_selection') == 'select') {
			$adminType = config('country.admin_type', 0);
			if (in_array($adminType, ['1', '2'])) {
				$rules['admin_code'] = ['required', 'not_in:0'];
			}
		}
		
		$phoneIsEnabledAsAuthField = (config('settings.sms.enable_phone_as_auth_field') == '1');
		$phoneNumberIsRequired = ($phoneIsEnabledAsAuthField && $this->input('auth_field') == 'phone');
		
		// email
		$emailIsRequired = (!$phoneNumberIsRequired);
		if ($emailIsRequired) {
			$rules['email'][] = 'required';
		}
		$rules = $this->validEmailRules('email', $rules);
		
		// phone
		if ($phoneNumberIsRequired) {
			$rules['phone'][] = 'required';
		}
		$rules = $this->validPhoneNumberRules('phone', $rules);
		
		// Custom Fields
		if (!isFromApi()) {
			$cfRequest = new CustomFieldRequest();
			$rules = $rules + $cfRequest->rules();
			$this->cfMessages = $cfRequest->messages();
		}
		
		// Tags
		if ($this->filled('tags')) {
			$rules['tags.*'] = [
				'regex:' . tagRegexPattern(),
				new BlacklistWordRule(),
			];
		}
		
		return $rules;
	}
	
	/**
	 * Get custom attributes for validator errors.
	 *
	 * @return array
	 */
	public function attributes(): array
	{
		$attributes = [];
		
		if ($this->file('pictures')) {
			$files = $this->file('pictures');
			foreach ($files as $key => $file) {
				$attributes['pictures.' . $key] = t('picture X', ['key' => ($key + 1)]);
			}
		}
		
		if ($this->filled('tags')) {
			$tags = $this->input('tags');
			if (is_array($tags) && !empty($tags)) {
				foreach ($tags as $key => $tag) {
					$attributes['tags.' . $key] = t('tag X', ['key' => ($key + 1)]);
				}
			}
		}
		
		return $attributes;
	}
	
	/**
	 * @return array
	 */
	public function messages(): array
	{
		$messages = [];
		
		// Category & Sub-Category
		if ($this->filled('parent_id') && !empty($this->input('parent_id'))) {
			$messages['category_id.required'] = t('The field is required', ['field' => mb_strtolower(t('sub_category'))]);
			$messages['category_id.not_in'] = t('The field is required', ['field' => mb_strtolower(t('sub_category'))]);
		}
		
		// Single Step Form
		if (config('settings.single.publication_form_type') == '2') {
			// Picture
			if ($this->file('pictures')) {
				$files = $this->file('pictures');
				foreach ($files as $key => $file) {
					// uploaded
					$maxSize = (int)config('settings.upload.max_image_size', 1000); // In KB
					$maxSize = $maxSize * 1024;                                     // Convert KB to Bytes
					$msg = t('large_file_uploaded_error', [
						'field'   => t('picture X', ['key' => ($key + 1)]),
						'maxSize' => readableBytes($maxSize),
					]);
					
					$uploadMaxFilesizeStr = @ini_get('upload_max_filesize');
					$postMaxSizeStr = @ini_get('post_max_size');
					if (!empty($uploadMaxFilesizeStr) && !empty($postMaxSizeStr)) {
						$uploadMaxFilesize = (int)strToDigit($uploadMaxFilesizeStr);
						$postMaxSize = (int)strToDigit($postMaxSizeStr);
						
						$serverMaxSize = min($uploadMaxFilesize, $postMaxSize);
						$serverMaxSize = $serverMaxSize * 1024 * 1024; // Convert MB to KB to Bytes
						if ($serverMaxSize < $maxSize) {
							$msg = t('large_file_uploaded_error_system', [
								'field'   => t('picture X', ['key' => ($key + 1)]),
								'maxSize' => readableBytes($serverMaxSize),
							]);
						}
					}
					
					$messages['pictures.' . $key . '.uploaded'] = $msg;
				}
			}
			
			// Package & PaymentMethod
			$messages['package_id.required'] = trans('validation.required_package_id');
			$messages['payment_method_id.required'] = t('validation.required_payment_method_id');
			$messages['payment_method_id.not_in'] = t('validation.required_payment_method_id');
		}
		
		// Custom Fields
		if (!isFromApi()) {
			$messages = $messages + $this->cfMessages;
		}
		
		return $messages;
	}
}
