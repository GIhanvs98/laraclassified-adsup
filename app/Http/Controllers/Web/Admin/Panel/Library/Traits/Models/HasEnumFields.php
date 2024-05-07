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

namespace App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Methods for ENUM and SELECT crud fields.
|--------------------------------------------------------------------------
*/

trait HasEnumFields
{
	public static function getPossibleEnumValues(string $fieldName): array
	{
		$instance = new static(); // Create an instance of the model to be able to get the table name
		$connectionName = $instance->getConnectionName();
		$table = DB::getTablePrefix() . $instance->getTable();
		
		try {
			$sql = 'SHOW COLUMNS FROM ' . $table . ' WHERE Field = "' . $fieldName . '"';
			$type = DB::connection($connectionName)->select($sql)[0]->Type;
		} catch (\Throwable $e) {
			$type = '';
		}
		
		$enum = [];
		
		if (!empty($type)) {
			preg_match('/^enum\((.*)\)$/', $type, $matches);
			$exploded = explode(',', $matches[1]);
			foreach ($exploded as $value) {
				$enum[] = trim($value, "'");
			}
		}
		
		return $enum;
	}
	
	public static function getEnumValuesAsAssociativeArray(string $fieldName): array
	{
		$instance = new static();
		$enumValues = $instance->getPossibleEnumValues($fieldName);
		
		$array = array_flip($enumValues);
		
		foreach ($array as $key => $value) {
			$array[$key] = $key;
		}
		
		return $array;
	}
}
