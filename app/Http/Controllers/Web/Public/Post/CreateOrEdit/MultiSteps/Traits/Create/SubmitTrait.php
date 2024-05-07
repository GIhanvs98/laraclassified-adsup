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

namespace App\Http\Controllers\Web\Public\Post\CreateOrEdit\MultiSteps\Traits\Create;

use App\Helpers\Files\Upload;
use App\Models\CategoryField;
use App\Models\Package;
use App\Models\Post;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use Illuminate\Http\Request;

trait SubmitTrait
{
	/**
	 * Store all input data in database
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	private function storeInputDataInDatabase(Request $request)
	{
		// Get all saved input data
		$postInput = (array)$request->session()->get('postInput');
		$picturesInput = (array)$request->session()->get('picturesInput');
		$paymentInput = (array)$request->session()->get('paymentInput');
		
		// Create the global input to send for database saving
		$inputArray = $postInput;
		if (isset($inputArray['category_id'], $inputArray['cf'])) {
			$fields = CategoryField::getFields($inputArray['category_id']);
			if ($fields->count() > 0) {
				foreach ($fields as $field) {
					if ($field->type == 'file') {
						if (isset($inputArray['cf'][$field->id]) && !empty($inputArray['cf'][$field->id])) {
							$inputArray['cf'][$field->id] = Upload::fromPath($inputArray['cf'][$field->id]);
						}
					}
				}
			}
		}
		
		$inputArray['pictures'] = [];
		if (!empty($picturesInput)) {
			foreach ($picturesInput as $key => $filePath) {
				if (!empty($filePath)) {
					$uploadedFile = Upload::fromPath($filePath);
					$inputArray['pictures'][] = $uploadedFile;
				}
			}
		}
		$inputArray = array_merge($inputArray, $paymentInput);
		
		request()->merge($inputArray);
		
		if (!empty($inputArray['pictures'])) {
			request()->files->set('pictures', $inputArray['pictures']);
		}
		
		// Call API endpoint
		$endpoint = '/posts';
		$data = makeApiRequest('post', $endpoint, request()->all(), [], true);
		
		// dd($data);
		
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
			
			// Save the post's ID in session
			$postId = data_get($data, 'result.id');
			if (!empty($postId)) {
				$request->session()->put('postId', $postId);
			}
			
			// Clear Temporary Inputs & Files
			$this->clearTemporaryInput();
		} else {
			flash($message)->error();
			
			return redirect()->back()->withInput($request->except('pictures'));
		}
		
		// Get the next URL
		$nextUrl = url('posts/create/finish');
		
		if (!empty($paymentInput)) {
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
