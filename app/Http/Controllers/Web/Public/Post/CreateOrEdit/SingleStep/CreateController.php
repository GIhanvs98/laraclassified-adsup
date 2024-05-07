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
use App\Http\Controllers\Web\Public\Post\CreateOrEdit\Traits\PricingPageUrlTrait;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Package;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Http\Controllers\Web\Public\FrontController;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class CreateController extends FrontController
{
	use VerificationTrait;
	use RequiredInfoTrait;
	use SingleStepPaymentTrait, MakePaymentTrait;
	use PricingPageUrlTrait;
	
	public $request;
	public $data;
	
	// Payment's properties
	public $msg = [];
	public $uri = [];
	public $selectedPackage = null; // See SingleStepPaymentTrait::paymentSettings()
	public $packages;
	public $paymentMethods;
	
	/**
	 * CreateController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Check if guests can post Ads
		if (config('settings.single.guests_can_post_listings') != '1') {
			$this->middleware('auth');
		}
		
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			
			return $next($request);
		});
	}
	
	/**
	 * @return void
	 * @throws \Exception
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
	 * New Post's Form.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getForm()
	{
		// Check if the 'Pricing Page' must be started first, and make redirection to it.
		$pricingUrl = $this->getPricingPage($this->selectedPackage);
		if (!empty($pricingUrl)) {
			return redirect($pricingUrl)->withHeaders(config('larapen.core.noCacheHeaders'));
		}
		
		// Check if the form type is 'Multi Steps Form', and make redirection to it (permanently).
		if (config('settings.single.publication_form_type') == '1') {
			$url = url('posts/create');
			
			return redirect($url, 301)->withHeaders(config('larapen.core.noCacheHeaders'));
		}
		
		// Meta Tags
		[$title, $description, $keywords] = getMetaTag('create');
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		// Create
		return appView('post.createOrEdit.singleStep.create');
	}
	
	/**
	 * Store a new Post.
	 *
	 * @param PostRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postForm(PostRequest $request)
	{
		// Call API endpoint
		$endpoint = '/posts';
		$data = makeApiRequest('post', $endpoint, $request->all(), [], true);
		
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
			session()->put('message', $message);
		} else {
			flash($message)->error();
		}
		
		// Get Next URL
		$nextUrl = url('create/finish');
		
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
			$nextUrl = qsUrl($nextUrl, request()->only(['package']), null, false);
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
		
		$nextUrl = qsUrl($nextUrl, request()->only(['package']), null, false);
		
		return redirect($nextUrl);
	}
	
	/**
	 * Confirmation
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function finish()
	{
		if (!session()->has('message')) {
			return redirect('/');
		}
		
		// Clear Session
		if (session()->has('itemNextUrl')) {
			session()->forget('itemNextUrl');
		}
		
		if (session()->has('postId')) {
			// Get the Post
			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session()->get('postId'))
				->first();
			
			abort_if(empty($post), 404, t('post_not_found'));
			
			session()->forget('postId');
		}
		
		// Redirect to the Post,
		// - If User is logged
		// - Or if Email and Phone verification option is not activated
		$doesVerificationIsDisabled = (config('settings.mail.email_verification') != 1 && config('settings.sms.phone_verification') != 1);
		if (auth()->check() || $doesVerificationIsDisabled) {
			if (isset($post) && !empty($post)) {
				flash(session()->get('message'))->success();
				
				return redirect(UrlGen::postUri($post));
			}
		}
		
		// Meta Tags
		MetaTag::set('title', session('message'));
		MetaTag::set('description', session('message'));
		
		return appView('post.createOrEdit.singleStep.finish');
	}
}
