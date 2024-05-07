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
use App\Observers\PackageObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class Package extends BaseModel
{
	use Crud, HasFactory, HasTranslations;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'packages';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = ['description_array', 'description_string', 'price_formatted'];

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
		'name',
		'short_name',
		'ribbon',
		'has_badge',
		'price',
		'currency_code',
		'promo_duration',
		'duration',
		'pictures_limit',
		'description',
		'facebook_ads_duration',
		'google_ads_duration',
		'twitter_ads_duration',
		'linkedin_ads_duration',
		'recommended',
		'active',
		'parent_id',
		'lft',
		'rgt',
		'depth',
		'packge_type'
	];
	public $translatable = ['name', 'short_name', 'description'];

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

		Package::observe(PackageObserver::class);

		static::addGlobalScope(new ActiveScope());
	}

	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->id . '/edit';
		$badge = '';
		if (!empty($this->short_name)) {
			$badge = ' <span class="badge bg-primary float-right">' . $this->short_name . '</span>';
		}

		return '<a href="' . $url . '">' . $this->name . '</a>' . $badge;
	}

	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function country()
	{
		return $this->belongsTo(Country::class, 'country_code', 'code');
	}

	public function currency()
	{
		return $this->belongsTo(Currency::class, 'currency_code', 'code');
	}

	public function payments()
	{
		return $this->hasMany(Payment::class, 'package_id');
	}

	public function posts()
	{
		return $this->hasMany(Post::class, 'package_id', 'id');
	}

	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeApplyCurrency($builder)
	{
		if (config('settings.geo_location.local_currency_packages_activation')) {
			$builder->where('currency_code', config('country.currency'));
		}

		return $builder;
	}

	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function descriptionArray(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->getDescriptionArray($value),
		);
	}

	protected function descriptionString(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!isset($this->description_array)) {
					return null;
				}

				$description = '';

				$options = $this->description_array;
				if (is_array($options)) {
					$options = array_filter($options, function ($value) {
						return !is_null($value) && $value !== '';
					});
					$options = array_unique($options);
					if (count($options) > 0) {
						$description .= implode(". \n", $options);
					}
				}

				return $description;
			},
		);
	}

	protected function priceFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$currency = [];
				if ($this->relationLoaded('currency')) {
					if (!empty($this->currency)) {
						$currency = $this->currency->toArray();
					}
				}

				return Number::money($this->price, $currency);
			},
		);
	}

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
	private function getDescriptionArray($value)
	{
		$locale = app()->getLocale();

		$description = [];

		if (isset($this->promo_duration) && $this->promo_duration > 0) {
			$description[] = trans_choice(
				'global.duration_of_promotion',
				getPlural($this->promo_duration),
				['number' => $this->promo_duration],
				$locale
			);
		}

		if (isset($this->facebook_ads_duration) && $this->facebook_ads_duration > 0) {
			$description[] = trans_choice(
				'global.facebook_ads_included',
				getPlural($this->facebook_ads_duration),
				['number' => $this->facebook_ads_duration],
				$locale
			);
		}

		if (isset($this->google_ads_duration) && $this->google_ads_duration > 0) {
			$description[] = trans_choice(
				'global.google_ads_included',
				getPlural($this->google_ads_duration),
				['number' => $this->google_ads_duration],
				$locale
			);
		}

		if (isset($this->twitter_ads_duration) && $this->twitter_ads_duration > 0) {
			$description[] = trans_choice(
				'global.twitter_ads_included',
				getPlural($this->twitter_ads_duration),
				['number' => $this->twitter_ads_duration],
				$locale
			);
		}

		if (isset($this->linkedin_ads_duration) && $this->linkedin_ads_duration > 0) {
			$description[] = trans_choice(
				'global.linkedin_ads_included',
				getPlural($this->linkedin_ads_duration),
				['number' => $this->linkedin_ads_duration],
				$locale
			);
		}

		if (isset($this->pictures_limit) && $this->pictures_limit >= config('settings.single.pictures_limit')) {
			$description[] = t('package_pictures_limit_allowed', ['limit' => $this->pictures_limit]);
		}

		$otherOptions = [];
		if (isset($this->description)) {
			$otherOptions = preg_split('#[\n;\.]+#ui', $this->description);
			$otherOptions = array_filter($otherOptions, function ($value) {
				return !is_null($value) && $value !== '';
			});
			$otherOptions = array_unique($otherOptions);
			if (is_array($otherOptions) && count($otherOptions) > 0) {
				foreach ($otherOptions as $option) {
					$description[] = $option;
				}
			}
		}

		if (isset($this->duration) && $this->duration > 0) {
			$description[] = t('duration_of_publication', ['number' => $this->duration]);
		}

		if (
			array_key_exists('promo_duration', $this->attributes)
			&& array_key_exists('facebook_ads_duration', $this->attributes)
			&& array_key_exists('google_ads_duration', $this->attributes)
			&& array_key_exists('twitter_ads_duration', $this->attributes)
			&& array_key_exists('linkedin_ads_duration', $this->attributes)
			&& array_key_exists('pictures_limit', $this->attributes)
			&& isset($otherOptions)
			&& array_key_exists('duration', $this->attributes)
		) {
			if (
				$this->promo_duration <= 0
				&& $this->facebook_ads_duration <= 0
				&& $this->google_ads_duration <= 0
				&& $this->twitter_ads_duration <= 0
				&& $this->linkedin_ads_duration <= 0
				&& $this->pictures_limit < config('settings.single.pictures_limit')
				&& empty($otherOptions)
				&& $this->duration <= 0
			) {
				$description[] = t('duration_of_publication', ['number' => (int)config('settings.cron.activated_listings_expiration', 30)]);
			}
		}

		return $description;
	}
}
