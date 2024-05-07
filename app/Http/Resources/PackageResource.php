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

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function toArray($request): array
	{
		$entity = [
			'id' => $this->id,
		];
		$columns = $this->getFillable();
		foreach ($columns as $column) {
			$entity[$column] = $this->{$column};
		}
		if (isset($this->description_array)) {
			$entity['description_array'] = $this->description_array;
		}
		if (isset($this->description_string)) {
			$entity['description_string'] = $this->description_string;
		}
		if (isset($this->price_formatted)) {
			$entity['price_formatted'] = $this->price_formatted;
		}
		
		$embed = explode(',', request()->get('embed'));
		
		if (in_array('currency', $embed)) {
			$entity['currency'] = new CurrencyResource($this->whenLoaded('currency'));
		}
		
		return $entity;
	}
}
