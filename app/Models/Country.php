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
use App\Helpers\Localization\Helpers\Country as CountryHelper;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\LocalizedScope;
use App\Observers\CountryObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class Country extends BaseModel
{
	use Crud, HasFactory, HasTranslations;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'countries';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'code';
	
	/**
	 * The "type" of the primary key ID.
	 *
	 * @var string
	 */
	protected $keyType = 'string';
	
	public $incrementing = false;
	protected $appends = [
		'icode',
		'flag_url',
		'flag16_url',
		'flag24_url',
		'flag32_url',
		'flag48_url',
		'flag64_url',
		'background_image_url'
	];
	protected $visible = [
		'code',
		'name',
		'icode',
		'iso3',
		'currency_code',
		'phone',
		'languages',
		'currency',
		'time_zone',
		'date_format',
		'datetime_format',
		'background_image',
		'flag_url',
		'flag16_url',
		'flag24_url',
		'flag32_url',
		'flag48_url',
		'flag64_url',
		'background_image_url',
		'admin_type',
	];
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	// public $timestamps = false;
	
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
		'code',
		'name',
		'capital',
		'continent_code',
		'tld',
		'currency_code',
		'phone',
		'languages',
		'time_zone',
		'date_format',
		'datetime_format',
		'background_image',
		'admin_type',
		'active',
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
	
	/**
	 * Country constructor.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		// CurrencyExchange plugin
		if (config('plugins.currencyexchange.installed')) {
			$this->visible[] = 'currencies';
			$this->fillable[] = 'currencies';
		}
		
		parent::__construct($attributes);
	}
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Country::observe(CountryObserver::class);
		
		static::addGlobalScope(new ActiveScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	/**
	 * Countries Batch Auto Translation
	 *
	 * @param bool $overwriteExistingTrans
	 */
	public static function autoTranslation($overwriteExistingTrans = false)
	{
		$tableName = (new self())->getTable();
		
		$languages = DB::table((new Language())->getTable())->get();
		$oldEntries = DB::table($tableName)->get();
		
		if ($oldEntries->count() > 0) {
			$transCountry = new CountryHelper();
			foreach ($oldEntries as $oldEntry) {
				$newNames = [];
				foreach ($languages as $language) {
					if (isJson($oldEntry->name)) {
						$oldNames = json_decode($oldEntry->name, true);
					}
					
					$translationNotFound = (!isset($oldNames[$language->abbr]) || empty($oldNames[$language->abbr]));
					
					if ($overwriteExistingTrans || $translationNotFound) {
						if ($translationNotFound) {
							$newNames[$language->abbr] = getColumnTranslation($oldEntry->name);
						}
						if ($name = $transCountry->get($oldEntry->code, $language->abbr)) {
							$newNames[$language->abbr] = $name;
						}
					}
				}
				if (!empty($newNames)) {
					$affected = DB::table($tableName)->where('code', $oldEntry->code)->update([
						'name' => json_encode($newNames, JSON_UNESCAPED_UNICODE),
					]);
				}
			}
		}
	}
	
	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->getKey() . '/edit';
		
		return '<a href="' . $url . '">' . $this->name . '</a>';
	}
	
	public function getActiveHtml()
	{
		if (!isset($this->active)) return '';
		
		return installAjaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'active', $this->active);
	}
	
	public function adminDivisions1Btn($xPanel = false): string
	{
		$url = admin_url('countries/' . $this->id . '/admins1');
		
		$msg = trans('admin.Admin Divisions 1 of country', ['country' => $this->name]);
		$toolTip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		$out = '<a class="btn btn-xs btn-light" href="' . $url . '"' . $toolTip . '>';
		$out .= '<i class="fa fa-eye"></i> ';
		$out .= mb_ucfirst(trans('admin.admin divisions 1'));
		$out .= '</a>';
		
		return $out;
	}
	
	public function citiesBtn($xPanel = false): string
	{
		$url = admin_url('countries/' . $this->id . '/cities');
		
		$msg = trans('admin.Cities of country', ['country' => $this->name]);
		$toolTip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		$out = '<a class="btn btn-xs btn-light" href="' . $url . '"' . $toolTip . '>';
		$out .= '<i class="fa fa-eye"></i> ';
		$out .= mb_ucfirst(trans('admin.cities'));
		$out .= '</a>';
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function currency()
	{
		return $this->belongsTo(Currency::class, 'currency_code', 'code');
	}
	
	public function continent()
	{
		return $this->belongsTo(Continent::class, 'continent_code', 'code');
	}
	
	public function posts()
	{
		return $this->hasMany(Post::class, 'country_code')->orderBy('created_at', 'DESC');
	}
	
	public function users()
	{
		return $this->hasMany(User::class, 'country_code')->orderBy('created_at', 'DESC');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeActive($query)
	{
		if (request()->segment(1) == admin_uri()) {
			if (str_contains(Route::currentRouteAction(), 'Admin\CountryController')) {
				return $query;
			}
		}
		
		return $query->where('active', 1);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function icode(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => strtolower($this->attributes['code']),
		);
	}
	
	protected function id(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->attributes['code'],
		);
	}
	
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
	
	protected function flagUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl();
			},
		);
	}
	
	protected function flag16Url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl(16);
			},
		);
	}
	
	protected function flag24Url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl(24);
			},
		);
	}
	
	protected function flag32Url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl(32);
			},
		);
	}
	
	protected function flag48Url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl(48);
			},
		);
	}
	
	protected function flag64Url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return $this->getFlagUrl(64);
			},
		);
	}
	
	protected function backgroundImageUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$bgImageUrl = null;
				if (isset($this->background_image) && !empty($this->background_image)) {
					$disk = StorageDisk::getDisk();
					if ($disk->exists($this->background_image)) {
						$bgImageUrl = imgUrl($this->background_image, 'bgHeader');
					}
				}
				
				return $bgImageUrl;
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
	private function getFlagUrl($size = 16) {
		$flagUrl = null;
		
		$missingIslandFlags = [
			'BQ' => 'NL',
			'BV' => 'NO',
			'GF' => 'FR',
			'GP' => 'FR',
			'PM' => 'FR',
			'RE' => 'FR',
			'SX' => 'NL',
		];
		$code = $missingIslandFlags[$this->code] ?? $this->code;
		
		$flagPath = 'images/flags/' . $size . '/' . strtolower($code) . '.png';
		if (file_exists(public_path($flagPath))) {
			$flagUrl = url($flagPath);
		}
		
		return $flagUrl;
	}
}
