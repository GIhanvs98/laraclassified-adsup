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

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Scopes\ActiveScope;
use App\Observers\HomeSectionObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

class HomeSection extends BaseModel
{
	use Crud, HasFactory;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'home_sections';
	
	protected $fakeColumns = ['value'];
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	
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
	protected $fillable = ['method', 'name', 'value', 'view', 'field', 'parent_id', 'lft', 'rgt', 'depth', 'active'];
	
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
	protected $casts = [
		'value' => 'array',
	];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		HomeSection::observe(HomeSectionObserver::class);
		
		static::addGlobalScope(new ActiveScope());
	}
	
	public function resetHomepageReOrderBtn($xPanel = false): string
	{
		$url = admin_url('homepage/reset_reorder');
		
		$msg = trans('admin.Reset the homepage sections reorder');
		$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		// Button
		$out = '<a class="btn btn-warning text-white shadow" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fas fa-sort-amount-up"></i> ';
		$out .= trans('admin.Reset sections reorganization');
		$out .= '</a>';
		
		return $out;
	}
	
	public function resetHomepageSettingsBtn($xPanel = false): string
	{
		$url = admin_url('homepage/reset_settings');
		
		$msg = trans('admin.Reset all the homepage settings');
		$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		// Button
		$out = '<a class="btn btn-danger shadow" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fas fa-industry"></i> ';
		$out .= trans('admin.Return to factory settings');
		$out .= '</a>';
		
		return $out;
	}
	
	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->getKey() . '/edit';
		
		return '<a href="' . $url . '">' . $this->name . '</a>';
	}
	
	public function configureBtn($xPanel = false): string
	{
		$url = admin_url('homepage/' . $this->id . '/edit');
		
		$msg = trans('admin.configure_entity', ['entity' => $this->name]);
		$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		$out = '<a class="btn btn-xs btn-primary" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fas fa-cog"></i> ';
		$out .= mb_ucfirst(trans('admin.Configure'));
		$out .= '</a>';
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function name(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->method)) {
					$transKey = 'settings.home_' . $this->method;
					
					if (trans()->has($transKey)) {
						$value = trans($transKey);
					}
				}
				
				return $value;
			},
		);
	}
	
	protected function field(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$diskName = StorageDisk::getDiskName();
				
				// Get 'field' field value
				$value = jsonToArray($value);
				
				$breadcrumb = trans('admin.Admin panel') . ' &rarr; '
					. mb_ucwords(trans('admin.settings')) . ' &rarr; '
					. mb_ucwords(trans('admin.homepage')) . ' &rarr; ';
				
				$formTitle = [
					[
						'name'         => 'group_name',
						'type'         => 'custom_html',
						'value'        => '<h2 class="setting-group-name">' . $this->name . '</h2>',
					],
					[
						'name'         => 'group_breadcrumb',
						'type'         => 'custom_html',
						'value'        => '<p class="setting-group-breadcrumb">' . $breadcrumb . $this->name . '</p>',
					],
				];
				
				// Handle 'field' field value
				// Get the right Setting
				$settingClassName = str($this->method)->camel()->ucfirst();
				$settingNamespace = '\\App\Models\HomeSection\\';
				$settingClass = $settingNamespace . $settingClassName;
				if (class_exists($settingClass)) {
					if (method_exists($settingClass, 'getFields')) {
						$value = $settingClass::getFields($diskName);
					}
				}
				
				return array_merge($formTitle, $value);
			},
		);
	}
	
	protected function value(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				// Get 'value' field value
				$value = jsonToArray($value);
				
				// Handle 'value' field value
				// Get the right Setting
				$settingClassName = str($this->method)->camel()->ucfirst();
				$settingNamespace = '\\App\Models\HomeSection\\';
				$settingClass = $settingNamespace . $settingClassName;
				if (class_exists($settingClass)) {
					if (method_exists($settingClass, 'getValues')) {
						$value = $settingClass::getValues($value);
					}
				}
				
				return $value;
			},
			set: function ($value) {
				$value = jsonToArray($value);
				
				// Handle 'value' field value
				// Get the right Setting
				$settingClassName = str($this->method)->camel()->ucfirst();
				$settingNamespace = '\\App\Models\HomeSection\\';
				$settingClass = $settingNamespace . $settingClassName;
				if (class_exists($settingClass)) {
					if (method_exists($settingClass, 'setValues')) {
						$value = $settingClass::setValues($value, $this);
					}
				}
				
				// Make sure that setting array contains only string, numeric or null elements
				$value = settingArrayElements($value);
				
				return (!empty($value)) ? json_encode($value) : null;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
