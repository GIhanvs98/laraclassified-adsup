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
use App\Observers\LanguageObserver;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

class Language extends BaseModel
{
	use Crud, HasFactory, Sluggable, SluggableScopeHelpers;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'languages';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'abbr';
	
	/**
	 * The "type" of the primary key ID.
	 *
	 * @var string
	 */
	protected $keyType = 'string';
	
	public $incrementing = false;
	
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
		'abbr',
		'locale',
		'name',
		'native',
		'flag',
		'app_name',
		'script',
		'direction',
		'russian_pluralization',
		'date_format',
		'datetime_format',
		'active',
		'default',
		'parent_id',
		'lft',
		'rgt',
		'depth',
		'created_at',
		'updated_at',
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
		
		Language::observe(LanguageObserver::class);
		
		static::addGlobalScope(new ActiveScope());
	}
	
	/**
	 * Return the sluggable configuration array for this model.
	 *
	 * @return array
	 */
	public function sluggable(): array
	{
		return [
			'app_name' => [
				'source' => ['app_name', 'name'],
			],
		];
	}
	
	/**
	 * @return array
	 */
	public static function getActiveLanguagesArray(): array
	{
		$cacheExpiration = config('settings.optimization.cache_expiration', 86400);
		$activeLanguages = cache()->remember('languages.active.array', $cacheExpiration, function () {
			return self::where('active', 1)->get();
		});
		
		return collect($activeLanguages)->keyBy('abbr')->toArray();
	}
	
	/**
	 * @param bool $abbr
	 * @return mixed
	 */
	public static function findByAbbr($abbr = false)
	{
		return self::where('abbr', $abbr)->first();
	}
	
	public function syncFilesLinesBtn($xPanel = false): string
	{
		$url = admin_url('languages/sync_files');
		
		$msg = trans('admin.Fill the missing lines in all languages files from the master language');
		$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		// Button
		$out = '<a class="btn btn-success shadow" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fas fa-exchange-alt"></i> ';
		$out .= trans('admin.Sync Languages Files Lines');
		$out .= '</a>';
		
		return $out;
	}
	
	public function filesLinesEditionBtn($xPanel = false): string
	{
		$url = admin_url('languages/texts');
		
		$msg = trans('admin.site_texts');
		$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
		
		// Button
		$out = '<a class="btn btn-primary shadow" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fa fa-language"></i> ';
		$out .= trans('admin.translate') . ' ' . mb_strtolower(trans('admin.site_texts'));
		$out .= '</a>';
		
		return $out;
	}
	
	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->getKey() . '/edit';
		
		return '<a href="' . $url . '">' . $this->name . '</a>';
	}
	
	public function getDefaultHtml(): string
	{
		return checkboxDisplay($this->default);
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
	public function scopeActive($query)
	{
		return $query->where('active', 1);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function id(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->attributes['abbr'],
		);
	}
	
	protected function native(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if ($value != '') {
					return $value;
				}
				return $this->attributes['name'];
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
