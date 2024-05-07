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
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'settings';
	
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
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'key', 'name', 'value', 'description', 'field', 'parent_id', 'lft', 'rgt', 'depth', 'active'];
	
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
	
	protected $casts = [
		'value' => 'array',
	];
	
	// Hide all these fake field content
	public static array $hiddenValues = [
		'purchase_code',
		'email',
		'phone_number',
		'smtp_username',
		'smtp_password',
		'mailgun_secret',
		'mailgun_username',
		'mailgun_password',
		'postmark_token',
		'postmark_username',
		'postmark_password',
		'ses_key',
		'ses_secret',
		'ses_username',
		'ses_password',
		'mandrill_secret',
		'mandrill_username',
		'mandrill_password',
		'sparkpost_secret',
		'sparkpost_username',
		'sparkpost_password',
		'sendmail_username',
		'sendmail_password',
		'vonage_key',
		'vonage_secret',
		'vonage_application_id',
		'vonage_from',
		'twilio_username',
		'twilio_password',
		'twilio_account_sid',
		'twilio_auth_token',
		'twilio_from',
		'twilio_alpha_sender',
		'twilio_sms_service_sid',
		'twilio_debug_to',
		'ipinfo_token',
		'dbip_api_key',
		'ipbase_api_key',
		'ip2location_api_key',
		'ipgeolocation_api_key',
		'iplocation_api_key',
		'ipstack_api_key', // old
		'ipstack_access_key',
		'maxmind_api_account_id',
		'maxmind_api_license_key',
		'maxmind_database_license_key',
		'recaptcha_v2_site_key',
		'recaptcha_v2_secret_key',
		'recaptcha_v3_site_key',
		'recaptcha_v3_secret_key',
		'recaptcha_site_key',
		'recaptcha_secret_key',
		'stripe_secret',
		'paypal_username',
		'paypal_password',
		'paypal_signature',
		'facebook_client_id',
		'facebook_client_secret',
		'linkedin_client_id',
		'linkedin_client_secret',
		'twitter_client_id',
		'twitter_client_secret',
		'google_client_id',
		'google_client_secret',
		'google_maps_key',
		'fixer_access_key',
		'currency_layer_access_key',
		'open_exchange_rates_app_id',
		'currency_data_feed_api_key',
		'forge_api_key',
		'xignite_token',
	];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Setting::observe(SettingObserver::class);
	}
	
	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->getKey() . '/edit';
		
		return '<a href="' . $url . '">' . $this->name . '</a>';
	}
	
	public function configureBtn($xPanel = false): string
	{
		$url = admin_url('settings/' . $this->id . '/edit');
		
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
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeActive($builder)
	{
		return $builder->where('active', 1);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function name(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->key)) {
					$transKey = 'settings.' . $this->key;
					
					if (trans()->has($transKey)) {
						$value = trans($transKey);
					}
				}
				
				return $value;
			},
		);
	}
	
	protected function description(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset($this->key)) {
					$transKey = 'settings.description_' . $this->key;
					
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
					. mb_ucwords(trans('admin.general_settings')) . ' &rarr; ';
				
				$formTitle = [
					[
						'name'  => 'group_name',
						'type'  => 'custom_html',
						'value' => '<h2 class="setting-group-name">' . $this->name . '</h2>',
					],
					[
						'name'  => 'group_breadcrumb',
						'type'  => 'custom_html',
						'value' => '<p class="setting-group-breadcrumb">' . $breadcrumb . $this->name . '</p>',
					],
				];
				
				// Handle 'field' field value
				// Get the right Setting
				$settingClassName = str($this->key)->camel()->ucfirst() . 'Setting';
				$settingNamespace = '\\App\Models\Setting\\';
				$settingClass = $settingNamespace . $settingClassName;
				if (class_exists($settingClass)) {
					if (method_exists($settingClass, 'getFields')) {
						$value = $settingClass::getFields($diskName);
					}
				} else {
					$settingNamespace = plugin_namespace($this->key) . '\app\Models\Setting\\';
					$settingClass = $settingNamespace . $settingClassName;
					// Get the plugin's setting
					if (class_exists($settingClass)) {
						if (method_exists($settingClass, 'getFields')) {
							$value = $settingClass::getFields($diskName);
						}
					}
				}
				
				return array_merge($formTitle, $value);
			},
		);
	}
	
	protected function value(): Attribute
	{
		return Attribute::make(
			get: fn($value) => $this->getValue($value),
			set: fn($value) => $this->setValue($value),
		);
	}
	
	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
	private function getValue($value)
	{
		// IMPORTANT
		// The line below means that the all Storage providers need to be load before the AppServiceProvider,
		// to prevent all errors during the retrieving of the settings in the AppServiceProvider.
		$disk = StorageDisk::getDisk();
		
		// Get 'value' field value
		$value = jsonToArray($value);
		
		// Handle 'value' field value
		// Get the right Setting
		$settingClassName = str($this->key)->camel()->ucfirst() . 'Setting';
		$settingNamespace = '\\App\Models\Setting\\';
		$settingClass = $settingNamespace . $settingClassName;
		if (class_exists($settingClass)) {
			if (method_exists($settingClass, 'getValues')) {
				$value = $settingClass::getValues($value, $disk);
			}
		} else {
			$settingNamespace = plugin_namespace($this->key) . '\app\Models\Setting\\';
			$settingClass = $settingNamespace . $settingClassName;
			// Get the plugin's setting
			if (class_exists($settingClass)) {
				if (method_exists($settingClass, 'getValues')) {
					$value = $settingClass::getValues($value, $disk);
				}
			}
		}
		
		// Demo: Secure some Data (Applied for all Entries)
		if (isAdminPanel() && isDemoDomain()) {
			$isNotFromAuthForm = (!in_array(request()->segment(2), ['password', 'login']));
			$value = collect($value)->mapWithKeys(function ($v, $k) use ($isNotFromAuthForm) {
				if ($isNotFromAuthForm && in_array($k, self::$hiddenValues)) {
					$v = '************************';
				}
				
				return [$k => $v];
			})->toArray();
		}
		
		return $value;
	}
	
	private function setValue($value)
	{
		// Get value
		$value = jsonToArray($value);
		
		// Handle 'value' field value
		// Get the right Setting
		$settingClassName = str($this->key)->camel()->ucfirst() . 'Setting';
		$settingNamespace = '\\App\Models\Setting\\';
		$settingClass = $settingNamespace . $settingClassName;
		if (class_exists($settingClass)) {
			if (method_exists($settingClass, 'setValues')) {
				$value = $settingClass::setValues($value, $this);
			}
		} else {
			$settingNamespace = plugin_namespace($this->key) . '\app\Models\Setting\\';
			$settingClass = $settingNamespace . $settingClassName;
			// Get the plugin's setting
			if (class_exists($settingClass)) {
				if (method_exists($settingClass, 'setValues')) {
					$value = $settingClass::setValues($value, $this);
				}
			}
		}
		
		// Make sure that setting array contains only string, numeric or null elements
		$value = settingArrayElements($value);
		
		return (!empty($value)) ? json_encode($value) : null;
	}
}
