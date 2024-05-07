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

namespace App\Http\Controllers\Api\Auth\Traits;

use App\Http\Resources\PasswordResetResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

trait VerificationTrait
{
	use EmailVerificationTrait, PhoneVerificationTrait, RecognizedUserActionsTrait;
	
	public array $entitiesRefs = [
		'users' => [
			'slug'      => 'users',
			'namespace' => '\\App\Models\User',
			'name'      => 'name',
			'scopes'    => [
				\App\Models\Scopes\VerifiedScope::class,
			],
		],
		'posts' => [
			'slug'      => 'posts',
			'namespace' => '\\App\Models\Post',
			'name'      => 'contact_name',
			'scopes'    => [
				\App\Models\Scopes\VerifiedScope::class,
				\App\Models\Scopes\ReviewedScope::class,
			],
		],
		'password' => [
			'slug'      => 'password',
			'namespace' => '\\App\Models\PasswordReset',
			'name'      => null,
			'scopes'    => [],
		],
	];
	
	/**
	 * Verification
	 *
	 * Verify the user's email address or mobile phone number
	 *
	 * @queryParam entitySlug string The slug of the entity to verify ('users' or 'posts'). Example: users
	 *
	 * @urlParam field string required The field to verify. Example: email
	 * @urlParam token string The verification token. Example: null
	 *
	 * @param $field
	 * @param $token
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function verification($field, $token = null)
	{
		if (empty($token)) {
			return $this->respondError(t('The token or code to verify is empty'));
		}
		
		$entitySlug = request()->get('entitySlug');
		
		// Get Entity
		$entityRef = $this->getEntityRef($entitySlug);
		if (empty($entityRef)) {
			return $this->respondNotFound(t('Entity ID not found'));
		}
		
		// Get Field Label
		$fieldLabel = t('email_address');
		if ($field == 'phone') {
			$fieldLabel = t('phone_number');
		}
		
		// Get Model (with its Namespace)
		$model = $entityRef['namespace'];
		
		// Verification (for Forgot Password)
		if ($entityRef['slug'] == 'password') {
			return $this->verificationForPassword($model, $fieldLabel, $token);
		}
		
		// Get Entity by Token
		$entity = $model::withoutGlobalScopes($entityRef['scopes'])->where($field . '_token', $token)->first();
		
		if (empty($entity)) {
			return $this->respondError(t('Your field verification has failed', ['field' => $fieldLabel]));
		}
		
		$data = [];
		$data['result'] = null;
		
		if (empty($entity->{$field . '_verified_at'})) {
			// Verified
			$entity->{$field . '_verified_at'} = now();
			$entity->save();
			
			$message = t('Your field has been verified', ['name' => $entity->{$entityRef['name']}, 'field' => $fieldLabel]);
			
			$data['success'] = true;
			$data['message'] = $message;
		} else {
			$message = t('Your field is already verified', ['field' => $fieldLabel]);
			
			$data['success'] = false;
			$data['message'] = $message;
			
			if ($entityRef['slug'] == 'users') {
				$data['result'] = new UserResource($entity);
			}
			if ($entityRef['slug'] == 'posts') {
				$data['result'] = new PostResource($entity);
			}
			
			return $this->apiResponse($data);
		}
		
		// Is It User Entity?
		if ($entityRef['slug'] == 'users') {
			$data['result'] = new UserResource($entity);
			
			// Match User's Posts (posted as Guest)
			$this->findAndMatchPostsToUser($entity);
			
			// Get User creation next URL
			// Login the User
			if (
				isVerifiedUser($entity)
				&& $entity->blocked != 1
				&& $entity->closed != 1
			) {
				// Create the API access token
				$deviceName = request()->input('device_name', 'Desktop Web');
				$token = $entity->createToken($deviceName);
				
				$extra = [];
				
				$extra['authToken'] = $token->plainTextToken;
				$extra['tokenType'] = 'Bearer';
				
				$data['extra'] = $extra;
			}
		}
		
		// Is It Listing Entity?
		if ($entityRef['slug'] == 'posts') {
			$data['result'] = new PostResource($entity);
			
			// Match User's listings (posted as Guest) & User's data (if missed)
			$this->findAndMatchUserToPost($entity);
		}
		
		return $this->apiResponse($data);
	}
	
	/**
	 * Verification (Forgot Password)
	 *
	 * Verify the user's email address or mobile phone number through the 'password_reset' table
	 *
	 * @param $model
	 * @param $fieldLabel
	 * @param $token
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function verificationForPassword($model, $fieldLabel, $token = null)
	{
		// Get Entity by Token
		$entity = $model::where('token', $token)->first();
		
		if (empty($entity)) {
			return $this->respondError(t('Your field verification has failed', ['field' => $fieldLabel]));
		}
		
		$message = t('your_field_has_been_verified_token', ['field' => $fieldLabel]);
		
		$data = [
			'success' => true,
			'message' => $message,
			'result'  => new PasswordResetResource($entity),
		];
		
		return $this->apiResponse($data);
	}
	
	/**
	 * @param null $entityRefId
	 * @return null
	 */
	public function getEntityRef($entityRefId = null)
	{
		if (empty($entityRefId)) {
			if (
				str_contains(Route::currentRouteAction(), 'Api\Auth\RegisterController')
				|| str_contains(Route::currentRouteAction(), 'Api\UserController')
				|| str_contains(Route::currentRouteAction(), 'Admin\UserController')
			) {
				$entityRefId = 'users';
			}
			
			if (
				str_contains(Route::currentRouteAction(), 'Api\PostController')
				|| str_contains(Route::currentRouteAction(), 'Admin\PostController')
			) {
				$entityRefId = 'posts';
			}
			
			/*
			 * Check the 'ForgotPasswordController' with these namespaces:
			 * - Api\Auth\ForgotPasswordController
			 * - Web\Public\Auth\ForgotPasswordController
			 * - Web\Admin\Auth\ForgotPasswordController
			 */
			if (str_contains(Route::currentRouteAction(), 'Auth\ForgotPasswordController')) {
				$entityRefId = 'password';
			}
		}
		
		// Get Entity
		return $this->entitiesRefs[$entityRefId] ?? null;
	}
}
