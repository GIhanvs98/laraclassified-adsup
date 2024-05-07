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

use App\Models\Scopes\ActiveScope;
use App\Observers\FieldObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class Field extends BaseModel
{
	use Crud, HasFactory, HasTranslations;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fields';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = [/*'options'*/];
	
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
		'belongs_to',
		'name',
		'type',
		'max',
		'default_value',
		'required',
		'use_as_filter',
		'help',
		'active',
	];
	public $translatable = ['name', 'default_value', 'help'];
	
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
		
		Field::observe(FieldObserver::class);
		
		static::addGlobalScope(new ActiveScope());
	}
	
	public static function fieldTypes()
	{
		return [
			'text'              => 'Text',
			'textarea'          => 'Textarea',
			'checkbox'          => 'Checkbox',
			'checkbox_multiple' => 'Checkbox (Multiple)',
			'select'            => 'Select Box',
			'radio'             => 'Radio',
			'file'              => 'File',
			'url'               => 'URL',
			'number'            => 'Number',
			'date'              => 'Date',
			'date_time'         => 'Date Time',
			'date_range'        => 'Date Range',
			'video'             => 'Video (Youtube, Vimeo)',
		];
	}
	
	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->id . '/edit';
		
		return '<a href="' . $url . '">' . $this->name . '</a>';
	}
	
	public function getTypeHtml()
	{
		$types = self::fieldTypes();
		
		return (isset($types[$this->type])) ? $types[$this->type] : $this->type;
	}
	
	public function optionsBtn($xPanel = false): string
	{
		$out = '';
		
		if (isset($this->type) && in_array($this->type, ['select', 'radio', 'checkbox_multiple'])) {
			$url = admin_url('custom_fields/' . $this->id . '/options');
			
			$out .= '<a class="btn btn-xs btn-info" href="' . $url . '">';
			$out .= '<i class="fa fa-cog"></i> ';
			$out .= mb_ucfirst(trans('admin.options'));
			$out .= '</a>';
		}
		
		return $out;
	}
	
	public function addToCategoryBtn($xPanel = false): string
	{
		$url = admin_url('custom_fields/' . $this->id . '/categories/create');
		
		$out = '<a class="btn btn-xs btn-light" href="' . $url . '">';
		$out .= '<i class="fa fa-plus"></i> ';
		$out .= trans('admin.Add to a Category');
		$out .= '</a>';
		
		return $out;
	}
	
	public function getRequiredHtml()
	{
		if (!isset($this->required)) return false;
		
		return checkboxDisplay($this->required);
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

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'category_field', 'field_id', 'category_id');
	}

	public function categoryFields(){

		return $this->hasMany(CategoryField::class, 'field_id', 'id');
	}
	
	public function options()
	{
		return $this->hasMany(FieldOption::class, 'field_id')
			->orderBy('lft')
			->orderBy('value');
	}

	public function unit()
	{
		return $this->hasOne(Field::class, 'id', 'unit_id');
	}
	
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
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
