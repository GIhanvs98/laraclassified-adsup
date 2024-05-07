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
use App\Helpers\Number;
use App\Helpers\RemoveFromString;
use App\Helpers\UrlGen;
use App\Models\Post\ReviewsPlugin;
use App\Models\Post\SimilarByCategory;
use App\Models\Post\SimilarByLocation;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Traits\CountryTrait;
use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Builder;

class Post extends BaseModel implements Feedable
{
	use Crud, CountryTrait, Notifiable, HasFactory, SimilarByCategory, SimilarByLocation, ReviewsPlugin;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	protected $appends = [
		'slug',
		'url',
		'phone_intl',
		'created_at_formatted',
		'user_photo_url',
		'country_flag_url',
		'count_pictures',
		'picture', /* Main Picture */
		'picture_url',
		'picture_url_small',
		'picture_url_medium',
		'picture_url_big',
		'price_label',
		'price_formatted',
	];

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
	protected $guarded = ['id'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'country_code',
		'user_id',
		'category_id',
		'post_type_id',
		'title',
		'description',
		'tags',
		'price',
		'currency_code',
		'negotiable',
		'contact_name',
		'auth_field',
		'email',
		'phone',
		'phone_national',
		'phone_country',
		'phone_hidden',
		'address',
		'city_id',
		'lat',
		'lon',
		'ip_addr',
		'visits',
		'tmp_token',
		'email_token',
		'phone_token',
		'email_verified_at',
		'phone_verified_at',
		'accept_terms',
		'accept_marketing_offers',
		'is_permanent',
		'reviewed_at',
		'featured',
		'archived_at',
		'archived_manually_at',
		'deletion_mail_sent_at',
		'fb_profile',
		'partner',
		'created_at',
		'updated_at',
		'is_approved',
		'bumped_at',
		'transaction_type',
		'price_unit',
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
		'email_verified_at' => 'datetime',
		'phone_verified_at' => 'datetime',
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
		'reviewed_at' => 'datetime',
		'archived_at' => 'datetime',
	];

	/*
							|--------------------------------------------------------------------------
							| FUNCTIONS
							|--------------------------------------------------------------------------
							*/
	protected static function boot()
	{
		parent::boot();

		Post::observe(PostObserver::class);

		static::addGlobalScope(new VerifiedScope());
		static::addGlobalScope(new ReviewedScope());
		static::addGlobalScope(new LocalizedScope());
	}

	public function routeNotificationForMail()
	{
		return $this->email;
	}

	public function routeNotificationForVonage()
	{
		$phone = phoneE164($this->phone, $this->phone_country);

		return setPhoneSign($phone, 'vonage');
	}

	public function routeNotificationForTwilio()
	{
		$phone = phoneE164($this->phone, $this->phone_country);

		return setPhoneSign($phone, 'twilio');
	}

	/**
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public static function getFeedItems()
	{
		$postsPerPage = (int) config('settings.list.items_per_page', 50);

		$posts = Post::reviewed()->unarchived();

		if (request()->filled('country') || config('plugins.domainmapping.installed')) {
			$countryCode = config('country.code');
			if (!config('plugins.domainmapping.installed')) {
				if (request()->filled('country')) {
					$countryCode = request()->get('country');
				}
			}
			$posts = $posts->where('country_code', $countryCode);
		}

		return $posts->take($postsPerPage)->orderByDesc('id')->get();
	}

	public function toFeedItem(): FeedItem
	{
		$title = $this->title;
		$title .= (isset($this->city) && !empty($this->city)) ? ' - ' . $this->city->name : '';
		$title .= (isset($this->country) && !empty($this->country)) ? ', ' . $this->country->name : '';
		// $summary = str_limit(str_strip(strip_tags($this->description)), 5000);
		$summary = transformDescription($this->description);
		$link = UrlGen::postUri($this, true);

		return FeedItem::create()
			->id($link)
			->title($title)
			->summary($summary)
			->category($this?->category?->name ?? '')
			->updated($this->updated_at)
			->link($link)
			->authorName($this->contact_name);
	}

	public function getTitleHtml(): string
	{
		$out = '';

		// $post = self::find($this->id);
		$out .= getPostUrl($this);
		$out .= '<br>';
		$out .= '<small>';
		$out .= $this->pictures->count() . ' ' . trans('admin.pictures');
		$out .= '</small>';
		if (isset($this->archived_at) && !empty($this->archived_at)) {
			$out .= '<br>';
			$out .= '<span class="badge bg-secondary">';
			$out .= trans('admin.Archived');
			$out .= '</span>';
		}

		return $out;
	}

	public function getPictureHtml(): string
	{
		// Get listing URL
		$url = url(UrlGen::postUri($this));

		$style = ' style="width:auto; max-height:90px;"';
		// Get first picture
		$out = '';
		if ($this->pictures->count() > 0) {
			$url = dmUrl($this->country_code, UrlGen::postPath($this));
			foreach ($this->pictures as $picture) {
				$out .= '<img src="' . imgUrl($picture->filename, 'small') . '" data-bs-toggle="tooltip" title="' . $this->title . '"' . $style . ' class="img-rounded">';
				break;
			}
		} else {
			// Default picture
			$out .= '<img src="' . imgUrl(config('larapen.core.picture.default'), 'small') . '" data-bs-toggle="tooltip" title="' . $this->title . '"' . $style . ' class="img-rounded">';
		}

		// Add link to the Ad
		return '<a href="' . $url . '" target="_blank">' . $out . '</a>';
	}

	public function getUserNameHtml()
	{
		if (isset($this->user) and !empty($this->user)) {
			$url = admin_url('users/' . $this->user->getKey() . '/edit');
			$tooltip = ' data-bs-toggle="tooltip" title="' . $this->user->name . '"';

			return '<a href="' . $url . '"' . $tooltip . '>' . $this->contact_name . '</a>';
		} else {
			return $this->contact_name;
		}
	}

	public function getCityHtml()
	{
		$out = $this->getCountryHtml();
		$out .= ' - ';
		if (isset($this->city) && !empty($this->city)) {
			$out .= '<a href="' . UrlGen::city($this->city) . '" target="_blank">' . $this->city->name . '</a>';
		} else {
			$out .= $this->city_id;
		}

		return $out;
	}

	public function getReviewedHtml(): string
	{
		return ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'reviewed_at', $this->reviewed_at);
	}

	public function getFeaturedHtml()
	{
		$out = '-';
		if (config('plugins.offlinepayment.installed')) {
			$opTool = '\extras\plugins\offlinepayment\app\Helpers\OpTools';
			if (class_exists($opTool)) {
				$out = $opTool::featuredCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'featured', $this->featured);
			}
		}

		return $out;
	}

	/*
							|--------------------------------------------------------------------------
							| QUERIES
							|--------------------------------------------------------------------------
							*/

	/*
							|--------------------------------------------------------------------------
							| RELATIONS
							|--------------------------------------------------------------------------
							*/
	public function postType()
	{
		return $this->belongsTo(PostType::class, 'post_type_id', 'id');
	}

	public function mainCategory()
	{
		return $this->belongsTo(SubCategoryGroup::class, 'sub_category_group_id', 'id');
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id');
	}

	public function currency()
	{
		return $this->belongsTo(Currency::class, 'currency_code', 'code');
	}

	public function latestPayment()
	{
		return $this->hasOne(Payment::class, 'post_id')->orderByDesc('id');
	}

	public function payments()
	{
		return $this->hasMany(Payment::class, 'post_id');
	}

	public function pictures()
	{
		return $this->hasMany(Picture::class, 'post_id')->orderBy('position', 'asc');
	}

	public function savedByLoggedUser()
	{
		$guard = isFromApi() ? 'sanctum' : null;
		$userId = (auth($guard)->check()) ? auth($guard)->user()->id : '-1';

		return $this->hasMany(SavedPost::class, 'post_id')->where('user_id', $userId);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function postValues()
	{
		return $this->hasMany(PostValue::class, 'post_id');
	}

	public function package()
	{
		return $this->belongsTo(Package::class, 'package_id', 'id');
	}

	public function reviewingViolation(): HasOne
	{
		return $this->hasOne(PostReviewingViolation::class, 'post_id', 'id');
	}

	/*
							|--------------------------------------------------------------------------
							| SCOPES
							|--------------------------------------------------------------------------
							*/
	public function scopeVerified($builder)
	{
		$builder->where(function ($query) {
			// $query->whereNotNull('email_verified_at')->whereNotNull('phone_verified_at');
		});

		$builder->whereNotNull('reviewed_at');

		return $builder;
	}

	public function scopeUnverified($builder)
	{
		$builder->where(function ($query) {
			// $query->whereNull('email_verified_at')->orWhereNull('phone_verified_at');
		});

		$builder->whereNull('reviewed_at');

		return $builder;
	}

	public function scopeArchived($builder)
	{
		return $builder->whereNotNull('archived_at');
	}

	public function scopeUnarchived($builder)
	{
		return $builder->whereNull('archived_at');
	}

	public function scopeReviewed($builder)
	{
		if (config('settings.single.listings_review_activation')) {
			return $builder->whereNotNull('reviewed_at');
		} else {
			return $builder;
		}
	}

	public function scopeUnreviewed($builder)
	{
		if (config('settings.single.listings_review_activation')) {
			return $builder->whereNull('reviewed_at');
		} else {
			return $builder;
		}
	}

	public function scopeWithCountryFix($builder)
	{
		// Check the Domain Mapping Plugin
		if (config('plugins.domainmapping.installed')) {
			return $builder->where('country_code', config('country.code'));
		} else {
			return $builder;
		}
	}

	/*
							|--------------------------------------------------------------------------
							| ACCESSORS | MUTATORS
							|--------------------------------------------------------------------------
							*/
	protected function createdAt(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = new Carbon($value);
				$value->timezone(Date::getAppTimeZone());

				return $value;
			},
		);
	}

	protected function updatedAt(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = new Carbon($value);
				$value->timezone(Date::getAppTimeZone());

				return $value;
			},
		);
	}

	protected function deletedAt(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty ($value)) {
					return null;
				}

				$value = new Carbon($value);
				$value->timezone(Date::getAppTimeZone());

				return $value;
			},
		);
	}

	protected function createdAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$createdAt = $this->attributes['created_at'] ?? null;
				if (empty ($createdAt)) {
					return null;
				}

				$value = new Carbon($createdAt);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	/*
							protected function archivedAt(): Attribute
							{
								return Attribute::make(
									get: function ($value) {
										if (empty($value)) {
											return null;
										}
										
										$value = new Carbon($value);
										$value->timezone(Date::getAppTimeZone());
										
										return $value;
									},
								);
							}
							*/

	protected function deletionMailSentAt(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty ($value)) {
					return null;
				}

				$value = new Carbon($value);
				$value->timezone(Date::getAppTimeZone());

				return $value;
			},
		);
	}

	protected function userPhotoUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				// Default Photo
				$defaultPhotoUrl = imgUrl(config('larapen.core.avatar.default'));

				// If the relation is not loaded through the Eloquent 'with()' method,
				// then don't make additional query. So the default value is returned.
				if (!$this->relationLoaded('user')) {
					return $defaultPhotoUrl;
				}

				// Photo from User's account
				$userPhotoUrl = $this->user?->photo_url ?? null;

				return (!empty ($userPhotoUrl) && $userPhotoUrl != $defaultPhotoUrl)
					? $userPhotoUrl
					: $defaultPhotoUrl;
			},
		);
	}

	protected function email(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!$this->relationLoaded('user')) {
					return $value;
				}

				if (isAdminPanel()) {
					if (
						isDemoDomain()
						&& request()->segment(2) != 'password'
					) {
						if (auth()->check()) {
							if (auth()->user()->getAuthIdentifier() != 1) {
								if (isset ($this->phone_token)) {
									if ($this->phone_token == 'demoFaker') {
										return $value;
									}
								}
								$value = hidePartOfEmail($value);
							}
						}
					}
				}

				return $value;
			},
		);
	}

	protected function phoneCountry(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$countryCode = $this->country_code ?? config('country.code');

				return !empty ($value) ? $value : $countryCode;
			},
		);
	}

	protected function phone(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return phoneE164($value, $this->phone_country);
			},
		);
	}

	protected function phoneNational(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = !empty ($value) ? $value : $this->phone;

				return phoneNational($value, $this->phone_country);
			},
		);
	}

	protected function phoneIntl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = (isset ($this->phone_national) && !empty ($this->phone_national))
					? $this->phone_national
					: $this->phone;

				if ($this->phone_country == config('country.code')) {
					return phoneNational($value, $this->phone_country);
				}

				return phoneIntl($value, $this->phone_country);
			},
		);
	}

	protected function title(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = mb_ucfirst($value);
				$cleanedValue = RemoveFromString::contactInfo($value, false, true);

				if (!$this->relationLoaded('user')) {
					return $cleanedValue;
				}

				if (!isAdminPanel()) {
					if (!empty ($this->user)) {
						if (!$this->user->hasAllPermissions(Permission::getStaffPermissions())) {
							$value = $cleanedValue;
						}
					} else {
						$value = $cleanedValue;
					}
				}

				return $value;
			},
		);
	}

	protected function slug(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = (is_null($value) && isset ($this->title)) ? $this->title : $value;

				$value = stripNonUtf($value);
				$value = slugify($value);

				// To prevent 404 error when the slug starts by a banned slug/prefix,
				// Add a tilde (~) as prefix to it.
				$bannedSlugs = regexSimilarRoutesPrefixes();
				foreach ($bannedSlugs as $bannedSlug) {
					if (str_starts_with($value, $bannedSlug)) {
						$value = '~' . $value;
						break;
					}
				}

				return $value;
			},
		);
	}

	/*
	 * For API calls, to allow listings sharing
	 */
	protected function url(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (isset ($this->id) && isset ($this->title)) {
					$path = str_replace(
						['{slug}', '{hashableId}', '{id}'],
						[$this->slug, hashId($this->id), $this->id],
						config('routes.post')
					);
				} else {
					$path = '#';
				}

				if (config('plugins.domainmapping.installed')) {
					$url = dmUrl($this->country_code, $path);
				} else {
					$url = url($path);
				}

				return $url;
			},
		);
	}

	protected function contactName(): Attribute
	{
		return Attribute::make(
			get: fn($value) => mb_ucwords($value),
		);
	}

	/*
	   
	   protected function description(): Attribute
	   {
		   return Attribute::make(
			   get: function ($value) {
				   if (isAdminPanel()) {
					   return $value;
				   }

				   $cleanedValue = RemoveFromString::contactInfo($value, false, true);

				   if (!$this->relationLoaded('user')) {
					   $value = $cleanedValue;
				   } else {
					   if (!empty($this->user)) {
						   if (!$this->user->hasAllPermissions(Permission::getStaffPermissions())) {
							   $value = $cleanedValue;
						   }
					   } else {
						   $value = $cleanedValue;
					   }
				   }

				   $apiValue = (isFromTheAppsWebEnvironment()) ? transformDescription($value) : str_strip(strip_tags($value));

				   return isFromApi() ? $apiValue : $value;
			   },
		   );
	   }

	   */

	protected function description(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				return strip_tags($value);
			},
			set: function ($value) {
				return strip_tags($value);
			},
		);
	}

	protected function tags(): Attribute
	{
		return Attribute::make(
			get: fn($value) => tagCleaner($value, true),
			set: function ($value) {
				if (is_array($value)) {
					$value = implode(',', $value);
				}

				return (!empty ($value)) ? mb_strtolower($value) : $value;
			},
		);
	}

	protected function countryFlagUrl(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$flagUrl = null;

				$flagPath = 'images/flags/16/' . strtolower($this->country_code) . '.png';
				if (file_exists(public_path($flagPath))) {
					$flagUrl = url($flagPath);
				}

				return $flagUrl;
			},
		);
	}

	protected function countPictures(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!$this->relationLoaded('pictures')) {
					return 0;
				}

				try {
					return $this->pictures->count();
				} catch (\Throwable $e) {
					return 0;
				}
			},
		);
	}

	protected function picture(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!$this->relationLoaded('pictures')) {
					return $this->getDefaultImg();
				}

				try {
					return $this->pictures->get(0)->filename;
				} catch (\Throwable $e) {
					return $this->getDefaultImg();
				}
			},
		);
	}

	protected function pictureUrl(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMainPictureUrl(),
		);
	}

	protected function pictureUrlSmall(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMainPictureUrl('small'),
		);
	}

	protected function pictureUrlMedium(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMainPictureUrl('medium'),
		);
	}

	protected function pictureUrlBig(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMainPictureUrl('big'),
		);
	}

	protected function priceLabel(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$defaultLabel = t('price') . ':';

				if (!$this->relationLoaded('category')) {
					return $defaultLabel;
				}

				$categoryType = $this->category?->type;

				$isJob = (in_array($categoryType, ['job-offer', 'job-search']));
				$isRent = ($categoryType == 'rent');
				$isNotSalable = ($categoryType == 'not-salable');

				$result = match (true) {
					$isJob => t('Salary') . ':',
					$isRent => t('Rent') . ':',
					$isNotSalable => null,
					default => $defaultLabel,
				};

				return (string) $result;
			},
		);
	}

	protected function priceFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$defaultValue = t('Contact us');

				// Relation with Category
				if (!$this->relationLoaded('category')) {
					return $defaultValue;
				}

				$categoryType = $this->category?->type;
				$price = $this?->price;

				$isNotSalable = ($categoryType == 'not-salable');
				$isNotFree = (is_numeric($price) && $price > 0);
				$isFree = (is_numeric($price) && $price == 0);

				// Relation with Currency
				$currency = [];
				if ($this->relationLoaded('currency')) {
					if (!empty ($this->currency)) {
						$currency = $this->currency->toArray();
					}
				}

				$result = match (true) {
					$isNotSalable => null,
					default => match (true) {
							$isNotFree => Number::money($price, $currency),
							$isFree => t('free_as_price'),
							default => $defaultValue,
						},
				};

				return (string) $result;
			},
		);
	}

	protected function negotiable(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!$this->relationLoaded('category')) {
					return 0;
				}

				$categoryType = $this->category?->type;
				$isNotSalable = ($categoryType == 'not-salable');

				return $isNotSalable ? 0 : $value;
			},
		);
	}

	/*
							|--------------------------------------------------------------------------
							| OTHER PRIVATE METHODS
							|--------------------------------------------------------------------------
							*/
	private function getMainPictureUrl(?string $size = null): ?string
	{
		if (!$this->relationLoaded('pictures')) {
			return $this->getDefaultImgUrl();
		}

		try {
			$size = !empty($size) ? '_' . $size : '';
			$pictureUrl = $this->pictures->get(0)->{'filename_url' . $size};

			return is_string($pictureUrl) ? $pictureUrl : null;
		} catch (\Throwable $e) {
			return $this->getDefaultImgUrl();
		}
	}

	private function getDefaultImg(): ?string
	{
		$defaultImg = config('larapen.core.picture.default');

		return (is_string($defaultImg)) ? $defaultImg : null;
	}

	private function getDefaultImgUrl(): string
	{
		return imgUrl($this->getDefaultImg());
	}

	public function adReports(): HasMany
	{
		return $this->hasMany(ReportAd::class);
	}

	public function transactions(): HasMany
	{
		return $this->hasMany(Transaction::class);
	}

	/**
	 * Get a transaction which is still currently applid for a service(membership/ad-promotion)
	 * @param \App\Models\Post $post
	 * @return void
	 */
	public static function deletePostWithoutPackageOrExpiredDuration(Post $post): void
	{
		if ($post->package) { # have package

			/**
			 * Not(Have a user & a membership except `Non Member` and if the selected membership have `post_untill_membership_duration : bool -> true`)
			 * Membership validity with transaction is verified by the `CheckMemberships` cron job
			 */
			if (!($post->user && $post->user->membership && $post->user->membership->member()->exists() && $post->user->membership->post_untill_membership_duration)) {

				if ($post->package->package_type == 'Top Ad' || $post->package->package_type == 'Bump Ad') {

					/**
					 * Deactivate expired ad-promotions inside transactions managed by `CheckAdPromotions` cron job
					 * We only have to select the active=0 ad-promotion transactions and delete the ads. 
					 */
					if ($post->transaction && $post->transaction->payment_type == 'ad-promotion' && !$post->transaction->active) { # deactive ad-promotion transaction
						$post->delete();
					} elseif (now() > $post->created_at->addDays($post->package->duration)) {
						$post->delete(); # Delete the post if no deactive transactions and post duration has exceeded
					}

				} elseif (now() > $post->created_at->addDays($post->package->duration)) { # Delete the post if it ia a package other than `Top ad` or a `Bump ad` or `Non Member`, and post duration has exceeded 
					$post->delete();
				}
			}
		} else { # no package. Becuse every ad should have a package Even a `Free package`
			$post->delete();
		}
	}

}
