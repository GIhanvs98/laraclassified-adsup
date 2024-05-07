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

use App\Models\Traits\ActiveTrait;
use App\Models\Traits\ColumnTrait;
use App\Models\Traits\VerifiedTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use ColumnTrait, ActiveTrait, VerifiedTrait;
}
