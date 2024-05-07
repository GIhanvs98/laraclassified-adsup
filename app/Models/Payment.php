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

use App\Helpers\Date;
use App\Helpers\UrlGen;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\StrictActiveScope;
use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

class Payment extends BaseModel
{
	use Crud, HasFactory;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'payments';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = ['created_at_formatted'];
	
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
		'post_id',
		'package_id',
		'payment_method_id',
		'transaction_id',
		'amount',
		'currency_code',
		'active',
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
		
		Payment::observe(PaymentObserver::class);
		
		static::addGlobalScope(new StrictActiveScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function getPostTitleHtml(): string
	{
		$out = '';
		
		$blankImage = '<img src="/images/blank.gif" style="width: 16px; height: 16px;" alt="">';
		
		if (empty($this->post_id)) {
			return $blankImage;
		}
		
		if (
			empty($this->post)
			|| (empty($this->post->country) && empty($this->post->country_code))
		) {
			$out .= $blankImage;
			$out .= ' ';
			$out .= '#' . $this->post_id;
			
			return $out;
		}
		
		$countryCode = $this->post->country->code ?? $this->post->country_code;
		$countryName = $this->post->country->name ?? $countryCode;
		
		// Post's Country
		$iconPath = 'images/flags/16/' . strtolower($countryCode) . '.png';
		if (file_exists(public_path($iconPath))) {
			$out .= '<a href="' . dmUrl($countryCode, '/', true, true) . '" target="_blank">';
			$out .= '<img src="' . url($iconPath) . getPictureVersion() . '" data-bs-toggle="tooltip" title="' . $countryName . '">';
			$out .= '</a>';
		} else {
			$out .= $blankImage;
		}
		$out .= ' ';
		
		// Post's ID
		$out .= '#' . $this->post_id;
		
		// Post's Title & Link
		// $postUrl = url(UrlGen::postUri($this->post));
		$postUrl = dmUrl($countryCode, UrlGen::postPath($this->post));
		$out .= ' - ';
		$out .= '<a href="' . $postUrl . '" target="_blank">' . $this->post->title . '</a>';
		
		if (config('settings.single.listings_review_activation')) {
			$outLeft = '<div class="float-left">' . $out . '</div>';
			$outRight = '<div class="float-right"></div>';
			
			if ($this->active != 1) {
				// Check if this listing has at least successful payment
				$countSuccessfulPayments = Payment::where('post_id', $this->post_id)->where('active', 1)->count();
				if ($countSuccessfulPayments <= 0) {
					$msg = trans('admin.payment_listing_delete_btn_tooltip');
					$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
					
					$outRight = '<div class="float-right">';
					$outRight .= '<a href="' . admin_url('posts/' . $this->post_id) . '" class="btn btn-xs btn-danger" data-button-type="delete"' . $tooltip . '>';
					$outRight .= '<i class="fa fa-trash"></i> ';
					$outRight .= trans('admin.Delete');
					$outRight .= '</a>';
					$outRight .= '</div>';
				}
			}
			
			$out = $outLeft . $outRight;
		}
		
		return $out;
	}
	
	public function getPackageNameHtml()
	{
		$out = $this->package_id;
		
		if (!empty($this->package)) {
			$packageUrl = admin_url('packages/' . $this->package_id . '/edit');
			
			$out = '<a href="' . $packageUrl . '">';
			$out .= $this->package->name;
			$out .= '</a>';
			$out .= ' (' . $this->package->price . ' ' . $this->package->currency_code . ')';
		}
		
		return $out;
	}
	
	public function getPaymentMethodNameHtml(): string
	{
		$out = '--';
		
		if (!empty($this->paymentMethod)) {
			$paymentMethodUrl = admin_url('payment_methods/' . $this->payment_method_id . '/edit');
			
			$out = '<a href="' . $paymentMethodUrl . '">';
			if ($this->paymentMethod->name == 'offlinepayment') {
				$out .= trans('offlinepayment::messages.Offline Payment');
			} else {
				$out .= $this->paymentMethod->display_name;
			}
			$out .= '</a>';
		}
		
		return $out;
	}
	
	public function getAmountHtml()
	{
		$out = $this->amount;
		
		if (isset($this->currency_code) && !empty($this->currency_code)) {
			$out .= ' ' . $this->currency_code;
		} else {
			if (!empty($this->package)) {
				$out .= ' ' . $this->package->currency_code;
			}
		}
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function post()
	{
		return $this->belongsTo(Post::class, 'post_id');
	}
	
	public function package()
	{
		return $this->belongsTo(Package::class, 'package_id', 'id');
	}
	
	public function paymentMethod()
	{
		return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
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
	protected function createdAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$createdAt = $this->attributes['created_at'] ?? $this->created_at ?? null;
				
				$value = new Carbon($createdAt);
				$value->timezone(Date::getAppTimeZone());
				
				return Date::formatFormNow($value);
			},
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
