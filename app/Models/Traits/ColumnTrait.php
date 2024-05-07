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

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ColumnTrait
{
	/**
	 * columnIsEmpty()
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $builder
	 * @param string $column
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeColumnIsEmpty(Builder $builder, string $column): Builder
	{
		$builder->where(function ($query) use ($column) {
			$query->where($column, '')->orWhere($column, 0)->orWhereNull($column);
		});
		
		return $builder;
	}
	
	/**
	 * columnIsNotEmpty()
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $builder
	 * @param string $column
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeColumnIsNotEmpty(Builder $builder, string $column): Builder
	{
		$builder->where(function ($query) use ($column) {
			$query->where($column, '!=', '')->where($column, '!=', 0)->whereNotNull($column);
		});
		
		return $builder;
	}
}
