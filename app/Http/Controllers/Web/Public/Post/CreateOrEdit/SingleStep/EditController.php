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

namespace App\Http\Controllers\Web\Public\Post\CreateOrEdit\SingleStep;

// Increase the server resources
$iniConfigFile = __DIR__ . '/../../../../../Helpers/Functions/ini.php';
if (file_exists($iniConfigFile)) {
	$configForUpload = true;
	include_once $iniConfigFile;
}

use App\Helpers\Referrer;
use App\Helpers\UrlGen;
use App\Http\Controllers\Api\Post\CreateOrEdit\Traits\RequiredInfoTrait;
use App\Http\Controllers\Api\Payment\SingleStepPaymentTrait;
use App\Http\Controllers\Api\Post\CreateOrEdit\Traits\MakePaymentTrait;
use App\Http\Controllers\Web\Public\Auth\Traits\VerificationTrait;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Package;
use App\Http\Controllers\Web\Public\FrontController;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class EditController extends FrontController
{
	use VerificationTrait;
	use RequiredInfoTrait;
	use SingleStepPaymentTrait, MakePaymentTrait;
	
	public $request;
	public $data;
	
	// Payment's properties
	public $msg = [];
	public $uri = [];
	public $packages;
	public $paymentMethods;
	
	/**
	 * EditController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			
			return $next($request);
		});
	}
	
	/**
	 * Common Queries
	 *
	 * @return void
	 */
	public function commonQueries(): void
	{
		$this->setPostFormRequiredInfo();
		$this->paymentSettings();
		
		// References
		$data = [];
		
		if (config('settings.single.show_listing_types')) {
			$data['postTypes'] = Referrer::getPostTypes($this->cacheExpiration);
			view()->share('postTypes', $data['postTypes']);
		}
		
		// Save common's data
		$this->data = $data;
	}
	
	/**
	 * Show the form the create a new listing post.
	 *
	 * @param $postId
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getForm($postId)
	{
		// Check if the form type is 'Multi Steps Form', and make redirection to it (permanently).
		if (config('settings.single.publication_form_type') == '1') {
			$url = url('posts/' . $postId . '/edit');
			
			return redirect($url, 301)->withHeaders(config('larapen.core.noCacheHeaders'));
		}
		
		$viewData = [];
		
		// Get post - Call API endpoint
		$endpoint = '/posts/' . $postId;
		$queryParams = [
			'embed'               => 'category,pictures,city,subAdmin1,subAdmin2,latestPayment,package',
			'countryCode'         => config('country.code'),
			'unactivatedIncluded' => 1,
			'belongLoggedUser'    => 1, // Logged user required
			'noCache'             => 1,
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$post = data_get($data, 'result');
		
		abort_if(empty($post), 404, t('post_not_found'));
		
		view()->share('post', $post);
		
		// Share the Post's Latest Payment Info (If exists)
		$this->getCurrentPaymentInfo($post);
		
		// Get the Post's City's Administrative Division
		$adminType = config('country.admin_type', 0);
		$admin = data_get($post, 'city.subAdmin' . $adminType);
		if (!empty($admin)) {
			view()->share('admin', $admin);
		}
		
		// Meta Tags
		MetaTag::set('title', t('update_my_listing'));
		MetaTag::set('description', t('update_my_listing'));
		
		return appView('post.createOrEdit.singleStep.edit', $viewData);
	}
	
	/**
	 * Update listing post.
	 *
	 * @param $postId
	 * @param PostRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postForm($postId, PostRequest $request)
	{
		// Call API endpoint
		$endpoint = '/posts/' . $postId;
		$data = makeApiRequest('put', $endpoint, $request->all(), [], true);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			flash($message)->error();
			
			if (data_get($data, 'extra.previousUrl')) {
				return redirect(data_get($data, 'extra.previousUrl'))->withInput($request->except('pictures'));
			} else {
				return redirect()->back()->withInput($request->except('pictures'));
			}
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
		} else {
			flash($message)->error();
		}
		
		// Get the next URL
		$nextUrl = UrlGen::postUri(data_get($data, 'result'));
		
		// Check if the payment process has been triggered
		// NOTE: Payment bypass email or phone verification
		if ($request->filled('package_id') && $request->filled('payment_method_id')) {
			$postId = data_get($data, 'result.id', 0);
			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', $postId)->with([
					'latestPayment' => function ($builder) { $builder->with(['package']); },
				])->first();
			if (!empty($post)) {
				// Make Payment (If needed) - By not using REST API
				// Check if the selected Package has been already paid for this Post
				$alreadyPaidPackage = false;
				if (!empty($post->latestPayment)) {
					if ($post->latestPayment->package_id == $request->input('package_id')) {
						$alreadyPaidPackage = true;
					}
				}
				// Check if Payment is required
				$package = Package::find($request->input('package_id'));
				if (!empty($package)) {
					if ($package->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {
						// Get the next URL
						$nextUrl = $this->apiUri['nextUrl'];
						$previousUrl = $this->apiUri['previousUrl'];
						
						// Send the Payment
						$paymentData = $this->sendPayment($request, $post);
						
						// Check if a Payment has been sent
						if (data_get($paymentData, 'extra.payment')) {
							$paymentMessage = data_get($paymentData, 'extra.payment.message');
							if (data_get($paymentData, 'extra.payment.success')) {
								flash($paymentMessage)->success();
								
								if (data_get($paymentData, 'extra.nextUrl')) {
									$nextUrl = data_get($paymentData, 'extra.nextUrl');
								}
								
								return redirect($nextUrl);
							} else {
								flash($paymentMessage)->error();
								
								if (data_get($paymentData, 'extra.previousUrl')) {
									$previousUrl = data_get($paymentData, 'extra.previousUrl');
								}
								
								return redirect($previousUrl)->withInput();
							}
						}
					}
				}
			}
		}
		
		// Get Listing Resource
		$post = data_get($data, 'result');
		
		if (
			data_get($data, 'extra.sendEmailVerification.emailVerificationSent')
			|| data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')
		) {
			session()->put('itemNextUrl', $nextUrl);
			
			if (data_get($data, 'extra.sendEmailVerification.emailVerificationSent')) {
				session()->put('emailVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationEmailLink($post, 'posts');
			}
			
			if (data_get($data, 'extra.sendPhoneVerification.phoneVerificationSent')) {
				session()->put('phoneVerificationSent', true);
				
				// Show the Re-send link
				$this->showReSendVerificationSmsLink($post, 'posts');
				
				// Go to Phone Number verification
				$nextUrl = url('posts/verify/phone/');
			}
		}
		
		// Mail Notification Message
		if (data_get($data, 'extra.mail.message')) {
			$mailMessage = data_get($data, 'extra.mail.message');
			if (data_get($data, 'extra.mail.success')) {
				flash($mailMessage)->success();
			} else {
				flash($mailMessage)->error();
			}
		}
		
		return redirect($nextUrl);
	}
}
