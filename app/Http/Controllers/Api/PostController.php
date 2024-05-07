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

namespace App\Http\Controllers\Api;

use App\Events\PostWasVisited;
use App\Helpers\Arr;
use App\Http\Controllers\Api\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Api\Payment\SingleStepPaymentTrait;
use App\Http\Controllers\Api\Picture\SingleStepPicturesTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\CommonUpdateTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\SingleStep\UpdateSingleStepFormTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\StoreTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\Traits\MakePaymentTrait;
use App\Http\Controllers\Api\Post\SearchTrait;
use App\Http\Controllers\Api\Post\ShowTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\MultiSteps\UpdateMultiStepsFormTrait;
use App\Http\Controllers\Api\Post\Traits\AutoRegistrationTrait;
use App\Http\Controllers\Api\Post\Traits\ShowFieldValueTrait;
use App\Http\Controllers\Api\Post\Traits\StoreFieldValueTrait;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\StrictActiveScope;
use App\Models\Scopes\VerifiedScope;
use App\Http\Resources\PostResource;
use App\Notifications\PostDeleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Twilio\TwilioChannel;

/**
 * @group Listings
 */
class PostController extends BaseController
{
	use SearchTrait, VerificationTrait, StoreFieldValueTrait;
	use ShowTrait, ShowFieldValueTrait;
	use AutoRegistrationTrait;
	use StoreTrait;
	use CommonUpdateTrait; // offline & repost
	use UpdateMultiStepsFormTrait;
	use UpdateSingleStepFormTrait, SingleStepPicturesTrait;
	use SingleStepPaymentTrait, MakePaymentTrait; // => StoreTrait & UpdateSingleStepFormTrait
	
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(function ($request, $next) {
			//...
			
			return $next($request);
		});
	}
	
	/**
	 * List listings
	 *
	 * @queryParam op string Type of listings list (optional) - Possible value: search,premium,latest,free,premiumFirst,similar. Example: null
	 * @queryParam postId int Base Listing's ID to get similar listings (optional) - Mandatory to get similar listings (when op=similar). Example: null
	 * @queryParam distance int Distance to get similar listings (optional) - Also optional when the type of similar listings is based on the current listing's category. Mandatory when the type of similar listings is based on the current listing's location. So, its usage is limited to get similar listings (when op=similar) based on the current listing's location. Example: null
	 * @queryParam belongLoggedUser boolean Do listings are belonged the logged user? Authentication token need to be sent in the header, and the "op" parameter need be null or unset - Possible value: 0 or 1.
	 * @queryParam pendingApproval boolean To list a user's listings in pending approval. Authentication token need to be sent in the header, and the "op" parameter need be null or unset - Possible value: 0 or 1.
	 * @queryParam archived boolean To list a user's archived listings. Authentication token need to be sent in the header, and the "op" parameter need be null or unset - Possible value: 0 or 1.
	 * @queryParam embed string Comma-separated list of the post relationships for Eager Loading - Possible values: user,category,parent,postType,city,currency,savedByLoggedUser,pictures,latestPayment,package. Example: null
	 * @queryParam sort string The sorting parameter (Order by DESC with the given column. Use "-" as prefix to order by ASC). Possible values: created_at. Example: created_at
	 * @queryParam perPage int Items per page. Can be defined globally from the admin settings. Cannot be exceeded 100. Example: 2
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function index(): \Illuminate\Http\JsonResponse
	{
		return $this->getPosts();
	}
	
	/**
	 * Get listing
	 *
	 * @queryParam unactivatedIncluded boolean Include or not unactivated entries - Possible value: 0 or 1. Example: 1
	 * @queryParam belongLoggedUser boolean Does the listing is belonged the logged user? - Possible value: 0 or 1. Example: 0
	 * @queryParam noCache boolean Disable the cache for this request - Possible value: 0 or 1. Example: 0
	 * @queryParam embed string Comma-separated list of the post relationships for Eager Loading - Possible values: user,category,parent,postType,city,currency,savedByLoggedUser,pictures,latestPayment,package,fieldsValues.Example: user,postType
	 * @queryParam detailed boolean Allow to get the listing's details with all its relationships (No need to set the 'embed' parameter). Example: false
	 *
	 * @urlParam id int required The post/listing's ID. Example: 2
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 * @throws \Exception
	 */
	public function show($id): \Illuminate\Http\JsonResponse
	{
		$isDetailed = (request()->filled('detailed') && request()->integer('detailed') == 1);
		
		if ($isDetailed) {
			
			$defaultEmbed = [
				'user',
				'category',
				'parent',
				'postType',
				'city',
				'currency',
				'savedByLoggedUser',
				'pictures',
				'latestPayment',
				'package',
			];
			if (request()->has('embed')) {
				$embed = explode(',', request()->get('embed', ''));
				$embed = array_merge($defaultEmbed, $embed);
				request()->query->set('embed', implode(',', $embed));
			} else {
				request()->query->add(['embed' => implode(',', $defaultEmbed)]);
			}
			
			return $this->showPost($id);
			
		} else {
			$embed = explode(',', request()->get('embed'));
			$countryCode = request()->get('countryCode');
			$isUnactivatedIncluded = (request()->filled('unactivatedIncluded') && request()->integer('unactivatedIncluded') == 1);
			$isBelongLoggedUser = (request()->filled('belongLoggedUser') && request()->integer('belongLoggedUser') == 1);
			
			// Cache control
			$this->updateCachingParameters();
			
			// Cache ID
			$cacheEmbedId = request()->filled('embed') ? '.embed.' . request()->get('embed') : '';
			$cacheFiltersId = '.filters' . '.unactivatedIncluded:' . (int)$isUnactivatedIncluded . '.auth:' . (int)$isBelongLoggedUser;
			$cacheId = 'post' . $cacheEmbedId . $cacheFiltersId . '.id:' . $id . '.' . config('app.locale');
			$cacheId = md5($cacheId);
			
			// Cached Query
			$post = cache()->remember($cacheId, $this->cacheExpiration, function () use ($countryCode, $isUnactivatedIncluded, $id, $embed, $isBelongLoggedUser) {
				$post = Post::query();
				
				if ($isUnactivatedIncluded) {
					$post->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class]);
				}
				
				if (in_array('country', $embed)) {
					$post->with('country');
				}
				if (in_array('user', $embed)) {
					$post->with('user');
				}
				if (in_array('category', $embed) || in_array('fieldsValues', $embed)) {
					$post->with('category');
				}
				if (in_array('postType', $embed)) {
					$post->with('postType');
				}
				if (in_array('city', $embed)) {
					$post->with('city');
					if (in_array('subAdmin1', $embed)) {
						$post->with('city.subAdmin1');
					}
					if (in_array('subAdmin2', $embed)) {
						$post->with('city.subAdmin2');
					}
				}
				if (in_array('latestPayment', $embed)) {
					$post->with('latestPayment');
					if (in_array('package', $embed)) {
						$post->with('latestPayment.package')->withoutGlobalScope(StrictActiveScope::class);
					}
				}
				if (in_array('savedByLoggedUser', $embed)) {
					$post->with('savedByLoggedUser');
				}
				if (in_array('pictures', $embed)) {
					$post->with('pictures');
				}
				
				if (!empty($countryCode)) {
					$post->whereHas('country')->countryOf($countryCode);
				}
				if ($isBelongLoggedUser) {
					$userId = (auth('sanctum')->check()) ? auth('sanctum')->user()->getAuthIdentifier() : '-1';
					$post->where('user_id', $userId);
				}
				
				return $post->where('id', $id)->first();
			});
			
			// Reset caching parameters
			$this->resetCachingParameters();
			
			abort_if(empty($post), 404, t('post_not_found'));
			
			// Increment the Listing's visits counter
			Event::dispatch(new PostWasVisited($post));
			
			$fieldsValues = [];
			if (in_array('fieldsValues', $embed)) {
				if (isset($post->category)) {
					$fieldsValues = $this->getFieldsValues($post->category->id, $post->id);
				}
			}
			
			$data = [
				'success' => true,
				'result'  => new PostResource($post),
				'extra' => [
					'fieldsValues' => $fieldsValues,
				],
			];
			
			return $this->apiResponse($data);
		}
	}
	
	/**
	 * Store listing
	 *
	 * For both types of listing's creation (Single step or Multi steps).
	 * Note: The field 'admin_code' is only available when the listing's country's 'admin_type' column is set to 1 or 2.
	 *
	 * @authenticated
	 * @header Authorization Bearer {YOUR_AUTH_TOKEN}
	 *
	 * @bodyParam country_code string required The code of the user's country. Example: US
	 * @bodyParam category_id int required The category's ID. Example: 1
	 * @bodyParam post_type_id int The listing type's ID. Example: 1
	 * @bodyParam title string required The listing's title. Example: John Doe
	 * @bodyParam description string required The listing's description. Example: Beatae placeat atque tempore consequatur animi magni omnis.
	 * @bodyParam contact_name string required The listing's author name. Example: John Doe
	 * @bodyParam auth_field string required The user's auth field ('email' or 'phone'). Example: email
	 * @bodyParam email string The listing's author email address (Required when 'auth_field' value is 'email'). Example: john.doe@domain.tld
	 * @bodyParam phone string The listing's author mobile number (Required when 'auth_field' value is 'phone'). Example: +17656766467
	 * @bodyParam phone_country string required The user's phone number's country code (Required when the 'phone' field is filled). Example: null
	 * @bodyParam admin_code string The administrative division's code. Example: 0
	 * @bodyParam city_id int required The city's ID.
	 * @bodyParam price int required The price. Example: 5000
	 * @bodyParam negotiable boolean Negotiable price or no. Example: 0
	 * @bodyParam phone_hidden boolean Mobile phone number will be hidden in public or no. Example: 0
	 * @bodyParam ip_addr string The listing's author IP address.
	 * @bodyParam accept_marketing_offers boolean Accept to receive marketing offers or no.
	 * @bodyParam is_permanent boolean Is it permanent post or no.
	 * @bodyParam tags string Comma-separated tags list. Example: car,automotive,tesla,cyber,truck
	 * @bodyParam accept_terms boolean required Accept the website terms and conditions. Example: 0
	 * @bodyParam pictures file[] required The listing's pictures.
	 * @bodyParam package_id int required The package's ID. Example: 2
	 * @bodyParam payment_method_id int The payment method's ID (required when the selected package's price is > 0). Example: 5
	 * @bodyParam captcha_key string Key generated by the CAPTCHA endpoint calling (Required when the CAPTCHA verification is enabled from the Admin panel).
	 *
	 * @param \App\Http\Requests\PostRequest $request
	 * @return array|\Illuminate\Http\JsonResponse|mixed
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function store(PostRequest $request)
	{
		$this->paymentSettings();
		
		return $this->storePost($request);
	}
	
	/**
	 * Update listing
	 *
	 * Note: The fields 'pictures', 'package_id' and 'payment_method_id' are only available with the single step post edition.
	 * The field 'admin_code' is only available when the listing's country's 'admin_type' column is set to 1 or 2.
	 *
	 * @authenticated
	 * @header Authorization Bearer {YOUR_AUTH_TOKEN}
	 *
	 * @bodyParam country_code string required The code of the user's country. Example: US
	 * @bodyParam category_id int required The category's ID. Example: 1
	 * @bodyParam post_type_id int The listing type's ID. Example: 1
	 * @bodyParam title string required The listing's title. Example: John Doe
	 * @bodyParam description string required The listing's description. Example: Beatae placeat atque tempore consequatur animi magni omnis.
	 * @bodyParam contact_name string required The listing's author name. Example: John Doe
	 * @bodyParam auth_field string required The user's auth field ('email' or 'phone'). Example: email
	 * @bodyParam email string The listing's author email address (Required when 'auth_field' value is 'email'). Example: john.doe@domain.tld
	 * @bodyParam phone string The listing's author mobile number (Required when 'auth_field' value is 'phone'). Example: +17656766467
	 * @bodyParam phone_country string required The user's phone number's country code (Required when the 'phone' field is filled). Example: null
	 * @bodyParam admin_code string The administrative division's code. Example: 0
	 * @bodyParam city_id int required The city's ID.
	 * @bodyParam price int required The price. Example: 5000
	 * @bodyParam negotiable boolean Negotiable price or no. Example: 0
	 * @bodyParam phone_hidden boolean Mobile phone number will be hidden in public or no. Example: 0
	 * @bodyParam ip_addr string The listing's author IP address.
	 * @bodyParam accept_marketing_offers boolean Accept to receive marketing offers or no.
	 * @bodyParam is_permanent boolean Is it permanent post or no.
	 * @bodyParam tags string Comma-separated tags list. Example: car,automotive,tesla,cyber,truck
	 * @bodyParam accept_terms boolean required Accept the website terms and conditions. Example: 0
	 *
	 * @bodyParam pictures file[] required The listing's pictures.
	 * @bodyParam package_id int required The package's ID. Example: 2
	 * @bodyParam payment_method_id int The payment method's ID (Required when the selected package's price is > 0). Example: 5
	 *
	 * @urlParam id int required The post/listing's ID.
	 *
	 * @param $id
	 * @param \App\Http\Requests\PostRequest $request
	 * @return array|\Illuminate\Http\JsonResponse|mixed
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function update($id, PostRequest $request)
	{
		// Single Step Form
		if (config('settings.single.publication_form_type') == '2') {
			$this->paymentSettings();
			return $this->updateSingleStepForm($id, $request);
		}
		
		return $this->updateMultiStepsForm($id, $request);
	}
	
	/**
	 * Delete listing(s)
	 *
	 * @authenticated
	 * @header Authorization Bearer {YOUR_AUTH_TOKEN}
	 *
	 * @urlParam ids string required The ID or comma-separated IDs list of listing(s).
	 *
	 * @param $ids
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($ids): \Illuminate\Http\JsonResponse
	{
		if (!auth('sanctum')->check()) {
			return $this->respondUnAuthorized();
		}
		
		$user = auth('sanctum')->user();
		
		$data = [
			'success' => false,
			'message' => t('no_deletion_is_done'),
			'result'  => null,
		];
		
		$extra = [];
		
		// Get Entries ID (IDs separated by comma accepted)
		$ids = explode(',', $ids);
		
		// Delete
		$res = false;
		foreach ($ids as $postId) {
			$post = Post::query()
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', $user->id)
				->where('id', $postId)
				->first();
			
			if (!empty($post)) {
				$tmpPost = Arr::toObject($post->toArray());
				
				// Delete Entry
				$res = $post->delete();
				
				// Send an Email or SMS confirmation
				$emailNotificationCanBeSent = (config('settings.mail.confirmation') == '1' && !empty($tmpPost->email));
				$smsNotificationCanBeSent = (
					config('settings.sms.enable_phone_as_auth_field') == '1'
					&& config('settings.sms.confirmation') == '1'
					&& $tmpPost->auth_field == 'phone'
					&& !empty($tmpPost->phone)
					&& !isDemoDomain()
				);
				try {
					if ($emailNotificationCanBeSent) {
						Notification::route('mail', $tmpPost->email)->notify(new PostDeleted($tmpPost));
					}
					if ($smsNotificationCanBeSent) {
						$smsChannel = (config('settings.sms.driver') == 'twilio')
							? TwilioChannel::class
							: 'vonage';
						Notification::route($smsChannel, $tmpPost->phone)->notify(new PostDeleted($tmpPost));
					}
				} catch (\Throwable $e) {
					$extra['mail']['success'] = false;
					$extra['mail']['message'] = $e->getMessage();
				}
			}
		}
		
		// Confirmation
		if ($res) {
			$data['success'] = true;
			
			$count = count($ids);
			if ($count > 1) {
				$data['message'] = t('x entities have been deleted successfully', ['entities' => t('listings'), 'count' => $count]);
			} else {
				$data['message'] = t('1 entity has been deleted successfully', ['entity' => t('listing')]);
			}
		}
		
		$data['extra'] = $extra;
		
		return $this->apiResponse($data);
	}
}
