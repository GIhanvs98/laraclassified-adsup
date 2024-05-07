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

use App\Helpers\Number;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\LocalizedScope;
use App\Models\Traits\CountryTrait;
use App\Observers\CityObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class City extends BaseModel
{
	use Crud, HasFactory, HasTranslations, CountryTrait;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'cities';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = ['slug'];
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = true;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	// protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'country_code',
		'name',
		'latitude',
		'longitude',
		'subadmin1_code',
		'subadmin2_code',
		'population',
		'time_zone',
		'active',
		'order',
	];
	public $translatable = ['name'];
	
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
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
	];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		City::observe(CityObserver::class);
		
		static::addGlobalScope(new ActiveScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function getAdmin2Html()
	{
		$out = $this->subadmin2_code;
		
		if (isset($this->subAdmin2) && !empty($this->subAdmin2)) {
			$out = $this->subAdmin2->name;
		}
		
		return $out;
	}
	
	public function getAdmin1Html()
	{
		$out = $this->subadmin1_code;
		
		if (isset($this->subAdmin1) && !empty($this->subAdmin1)) {
			$out = $this->subAdmin1->name;
		}
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function posts()
	{
		return $this->hasMany(Post::class, 'city_id');
	}
	
	public function subAdmin2()
	{
		return $this->belongsTo(SubAdmin2::class, 'subadmin2_code', 'code');
	}
	
	public function subAdmin1()
	{
		return $this->belongsTo(SubAdmin1::class, 'subadmin1_code', 'code');
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
	protected function name(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->attributes['name']) && !isJson($this->attributes['name'])) {
					return $this->attributes['name'];
				}
				
				return $value;
			},
		);
	}
	
	protected function slug(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = (is_null($value) && isset($this->name)) ? $this->name : $value;
				
				return slugify($value);
			},
		);
	}
	
	protected function latitude(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => Number::toFloat($value),
		);
	}
	
	protected function longitude(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => Number::toFloat($value),
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
