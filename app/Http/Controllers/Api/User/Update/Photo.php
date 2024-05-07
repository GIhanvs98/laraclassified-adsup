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

namespace App\Http\Controllers\Api\User\Update;

use App\Helpers\Files\Upload;
use App\Http\Resources\UserResource;
use App\Models\User;

trait Photo
{
	/**
	 * Update the User's Photo
	 *
	 * @param \App\Models\User $user
	 * @param $request
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function updatePhoto(User $user, $request)
	{
		$data = [
			'success' => true,
			'result'  => null,
		];
		
		if (empty($user)) {
			$data['success'] = false;
			$data['message'] = t('user_not_found');
			
			return $data;
		}
		
		// Check logged User
		if (auth('sanctum')->user()->getAuthIdentifier() != $user->id) {
			$data['success'] = false;
			$data['message'] = t('Unauthorized action');
			
			return $data;
		}
		
		$file = $request->file('photo');
		if (empty($file)) {
			$data['success'] = false;
			$data['message'] = 'File is empty.';
			
			return $data;
		}
		
		$extra = [];
		
		// Upload & save the picture
		$param = [
			'destPath' => 'avatars/' . strtolower($user->country_code) . '/' . $user->id,
			'width'    => (int)config('larapen.core.picture.otherTypes.user.width', 800),
			'height'   => (int)config('larapen.core.picture.otherTypes.user.height', 800),
			'ratio'    => config('larapen.core.picture.otherTypes.user.ratio', '1'),
			'upsize'   => config('larapen.core.picture.otherTypes.user.upsize', '0'),
		];
		$user->photo = Upload::image($param['destPath'], $file, $param);
		$user->save();
		
		// Result data
		$data['message'] = t('Your photo or avatar have been updated');
		$data['result'] = (new UserResource($user))->toArray($request);
		
		if (isFromTheAppsWebEnvironment()) {
			// Get the FileInput plugin's data
			$fileInput = [];
			$fileInput['initialPreview'] = [];
			$fileInput['initialPreviewConfig'] = [];
			
			if (!empty($user->photo)) {
				// Get Deletion Url
				$initialPreviewConfigUrl = url('account/photo/delete');
				
				$photoSize = (isset($this->disk) && $this->disk->exists($user->photo))
					? (int)$this->disk->size($user->photo)
					: 0;
				
				// Extra Fields for AJAX file removal (related to the $initialPreviewConfigUrl)
				$initialPreviewConfigExtra = [
					'_token'       => csrf_token(),
					'_method'      => 'PUT',
					'name'         => $user->name,
					'phone'        => $user->phone,
					'email'        => $user->email,
					'remove_photo' => 1,
				];
				
				// Build Bootstrap-FileInput plugin's parameters
				$fileInput['initialPreview'][] = imgUrl($user->photo, 'user');
				
				$fileInput['initialPreviewConfig'][] = [
					'caption' => basename($user->photo),
					'size'    => $photoSize,
					'url'     => $initialPreviewConfigUrl,
					'key'     => $user->id,
					'extra'   => $initialPreviewConfigExtra,
				];
			}
			$extra['fileInput'] = $fileInput;
		}
		
		$data['extra'] = $extra;
		
		return $data;
	}
	
	/**
	 * Remove the User's photo
	 *
	 * @param \App\Models\User $user
	 * @param $request
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function removePhoto(User $user, $request)
	{
		$data = [
			'success' => true,
			'result'  => null,
		];
		
		if (empty($user)) {
			$data['success'] = false;
			$data['message'] = t('user_not_found');
		}
		
		// Check logged User
		if (auth('sanctum')->user()->getAuthIdentifier() != $user->id) {
			$data['success'] = false;
			$data['message'] = t('Unauthorized action');
		}
		
		if (!isset($data['success']) || !$data['success']) {
			return $data;
		}
		
		// Remove all the current user's photos, by removing his photos' directory.
		$destinationPath = substr($user->photo, 0, strrpos($user->photo, '/'));
		if (!empty($destinationPath) && $this->disk->exists($destinationPath)) {
			$this->disk->deleteDirectory($destinationPath);
		}
		
		// Delete the photo path from DB
		$user->photo = null;
		$user->save();
		
		$data['message'] = t('Your photo or avatar has been deleted');
		$data['result'] = (new UserResource($user))->toArray($request);
		
		return $data;
	}
}
