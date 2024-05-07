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

namespace App\Http\Controllers\Api\Picture;

use App\Helpers\Files\Upload;
use App\Http\Requests\PhotoRequest;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PostResource;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;

trait MultiStepsPicturesTrait
{
	/**
	 * Store Pictures (from Multi Steps Form)
	 *
	 * @param \App\Http\Requests\PhotoRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function storeMultiStepsPictures(PhotoRequest $request): \Illuminate\Http\JsonResponse
	{
		// Get customized request variables
		$countryCode = $request->input('country_code', config('country.code'));
		$countPackages = $request->integer('count_packages');
		$countPaymentMethods = $request->integer('count_payment_methods');
		$postId = $request->input('post_id');
		
		$user = null;
		if (auth('sanctum')->check()) {
			$user = auth('sanctum')->user();
		}
		
		$post = null;
		if (!empty($user) && !empty($postId)) {
			$post = Post::countryOf($countryCode)->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', $user->id)
				->where('id', $postId)
				->first();
		}
		
		if (empty($post)) {
			return $this->respondNotFound(t('post_not_found'));
		}
		
		$pictures = Picture::where('post_id', $post->id);
		
		// Get default/global pictures limit
		$defaultPicturesLimit = (int)config('settings.single.pictures_limit', 5);
		if ($post->featured == 1 && !empty($post->latestPayment)) {
			if (isset($post->latestPayment->package) && !empty($post->latestPayment->package)) {
				if (!empty($post->latestPayment->package->pictures_limit)) {
					$defaultPicturesLimit = $post->latestPayment->package->pictures_limit;
				}
			}
		}
		
		// Get pictures limit
		$countExistingPictures = $pictures->count();
		$picturesLimit = $defaultPicturesLimit - $countExistingPictures;
		
		if ($picturesLimit > 0) {
			// Get pictures initial position
			$latestPosition = $pictures->orderByDesc('position')->first();
			$initialPosition = (!empty($latestPosition) && (int)$latestPosition->position > 0) ? (int)$latestPosition->position : 0;
			$initialPosition = ($countExistingPictures >= $initialPosition) ? $countExistingPictures : $initialPosition;
			
			// Save all pictures
			$pictures = [];
			$files = $request->file('pictures');
			if (is_array($files) && count($files) > 0) {
				foreach ($files as $key => $file) {
					if (empty($file)) {
						continue;
					}
					
					// Delete old file if new file has uploaded
					// Check if current Listing have a pictures
					$picturePosition = $initialPosition + (int)$key + 1;
					$picture = Picture::query()
						->where('post_id', $post->id)
						->where('id', $key)
						->first();
					if (!empty($picture)) {
						$picturePosition = $picture->position;
						$picture->delete();
					}
					
					// Post Picture in database
					$picture = new Picture([
						'post_id'   => $post->id,
						'filename'  => null,
						'mime_type' => null,
						'position'  => $picturePosition,
					]);
					
					// Upload File
					$destPath = 'files/' . strtolower($post->country_code) . '/' . $post->id;
					$picture->filename = Upload::image($destPath, $file, null, true);
					$picture->mime_type = getUploadedFileMimeType($file);
					
					if (!empty($picture->filename)) {
						$picture->save();
					}
					
					$pictures[] = (new PictureResource($picture));
					
					// Check the pictures limit
					if ($key >= ($picturesLimit - 1)) {
						break;
					}
				}
			}
			
			if (!empty($pictures)) {
				$data = [
					'success' => true,
					'message' => t('The pictures have been updated'),
					'result'  => $pictures,
				];
			} else {
				$data = [
					'success' => false,
					'message' => t('error_found'),
					'result'  => null,
				];
			}
		} else {
			$pictures = [];
			$data = [
				'success' => false,
				'message' => t('pictures_limit_reached'),
				'result'  => null,
			];
		}
		
		$extra = [];
		
		$extra['post']['result'] = (new PostResource($post))->toArray($request);
		
		// Should it be gone on Payment page or not?
		if ($countPackages > 0 && $countPaymentMethods > 0) {
			$extra['steps']['payment'] = true;
			$extra['nextStepLabel'] = t('Next');
		} else {
			$extra['steps']['payment'] = false;
			$extra['nextStepLabel'] = t('Done');
		}
		
		if (isFromTheAppsWebEnvironment()) {
			// Get the FileInput plugin's data
			$fileInput = [];
			$fileInput['initialPreview'] = [];
			$fileInput['initialPreviewConfig'] = [];
			
			$pictures = collect($pictures);
			if ($pictures->count() > 0) {
				foreach ($pictures as $picture) {
					if (empty($picture->filename)) {
						continue;
					}
					
					// Get Deletion Url
					$initialPreviewConfigUrl = url('posts/' . $post->id . '/photos/' . $picture->id . '/delete');
					
					$pictureSize = (isset($this->disk) && $this->disk->exists($picture->filename))
						? (int)$this->disk->size($picture->filename)
						: 0;
					
					// Build Bootstrap-FileInput plugin's parameters
					$fileInput['initialPreview'][] = imgUrl($picture->filename, 'medium');
					$fileInput['initialPreviewConfig'][] = [
						'caption' => basename($picture->filename),
						'size'    => $pictureSize,
						'url'     => $initialPreviewConfigUrl,
						'key'     => $picture->id,
						'extra'   => ['id' => $picture->id],
					];
				}
			}
			$extra['fileInput'] = $fileInput;
		}
		
		$data['extra'] = $extra;
		
		return $this->apiResponse($data);
	}
	
	/**
	 * Delete a Picture (from Multi Steps Form)
	 *
	 * @param $pictureId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteMultiStepsPicture($pictureId): \Illuminate\Http\JsonResponse
	{
		// Get customized request variables
		$postId = request()->input('post_id');
		
		$user = null;
		if (auth('sanctum')->check()) {
			$user = auth('sanctum')->user();
		}
		
		// Get Post
		$post = null;
		if (!empty($user) && !empty($postId)) {
			$post = Post::query()
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', $user->id)
				->where('id', $postId)
				->first();
		}
		
		if (empty($post)) {
			return $this->respondNotFound(t('post_not_found'));
		}
		
		$pictures = Picture::withoutGlobalScopes([ActiveScope::class])->where('post_id', $postId);
		
		if ($pictures->count() <= 0) {
			return $this->respondUnAuthorized();
		}
		
		if ($pictures->count() == 1) {
			if (config('settings.single.picture_mandatory')) {
				return $this->respondUnAuthorized(t('the_latest_picture_removal_text'));
			}
		}
		
		$pictures = $pictures->get();
		foreach ($pictures as $picture) {
			if ($picture->id == $pictureId) {
				$res = $picture->delete();
				break;
			}
		}
		
		$message = t('The picture has been deleted');
		
		return $this->respondSuccess($message);
	}
	
	/**
	 * Reorder Pictures - Bulk Update
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function reorderMultiStepsPictures(): \Illuminate\Http\JsonResponse
	{
		// Get customized request variables
		$postId = request()->input('post_id');
		
		if (request()->header('X-Action') != 'bulk') {
			return $this->respondUnauthorized();
		}
		
		$bodyJson = request()->input('body');
		if (!isJson($bodyJson)) {
			return $this->respondError('Invalid JSON format for the "body" field.');
		}
		
		$bodyArray = json_decode($bodyJson);
		if (!is_array($bodyArray) || empty($bodyArray)) {
			return $this->respondNoContent();
		}
		
		$user = null;
		if (auth('sanctum')->check()) {
			$user = auth('sanctum')->user();
		}
		
		$pictures = [];
		foreach ($bodyArray as $item) {
			if (!isset($item->id) || !isset($item->position)) {
				continue;
			}
			if (empty($item->id) || !is_numeric($item->position)) {
				continue;
			}
			
			$picture = null;
			if (!empty($user) && !empty($postId)) {
				$picture = Picture::where('id', $item->id)
					->whereHas('post', function ($query) use ($user) {
						$query->where('user_id', $user->id);
					})->first();
			}
			
			if (!empty($picture)) {
				$picture->position = $item->position;
				$picture->save();
				
				$pictures[] = (new PictureResource($picture));
			}
		}
		
		// Get endpoint output data
		$data = [
			'success' => !empty($pictures),
			'message' => !empty($pictures) ? t('Your picture has been reorder successfully') : null,
			'result'  => !empty($pictures) ? $pictures : null,
		];
		
		return $this->apiResponse($data);
	}
}
