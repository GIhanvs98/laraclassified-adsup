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

use App\Helpers\Arr;
use App\Helpers\UrlGen;

/**
 * @param string|null $path
 * @return string
 */
function admin_uri(?string $path = ''): string
{
	$path = str_replace(url(config('larapen.admin.route', 'admin')), '', $path);
	$path = ltrim($path, '/');
	
	if (!empty($path)) {
		$path = config('larapen.admin.route', 'admin') . '/' . $path;
	} else {
		$path = config('larapen.admin.route', 'admin');
	}
	
	return $path;
}

/**
 * @param string|null $path
 * @return string
 */
function admin_url(?string $path = ''): string
{
	return url(admin_uri($path));
}

/**
 * Checkbox Display
 *
 * @param $fieldValue
 * @return string
 */
function checkboxDisplay($fieldValue): string
{
	// fa-square-o | fa-check-square-o
	// fa-toggle-off | fa-toggle-on
    if (!empty($fieldValue)) {
        return '<i class="admin-single-icon fa fa-toggle-on" aria-hidden="true"></i>';
    } else {
        return '<i class="admin-single-icon fa fa-toggle-off" aria-hidden="true"></i>';
    }
}

/**
 * Ajax Checkbox Display
 *
 * @param $id
 * @param $table
 * @param $field
 * @param null $fieldValue
 * @return string
 */
function ajaxCheckboxDisplay($id, $table, $field, $fieldValue = null): string
{
    $lineId = $field.$id;
    $lineId = str_replace('.', '', $lineId); // fix JS bug (in admin layout)
    $data = 'data-table="' . $table . '"
			data-field="'.$field.'"
			data-line-id="' . $lineId . '"
			data-id="' . $id . '"
			data-value="' . $fieldValue . '"';

    // Decoration
    if (isset($fieldValue) && !empty($fieldValue)) {
        $html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-on" aria-hidden="true"></i>';
    } else {
        $html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-off" aria-hidden="true"></i>';
    }
	
	return '<a href="" class="ajax-request" ' . $data . '>' . $html . '</a>';
}

/**
 * Advanced Ajax Checkbox Display
 *
 * @param $id
 * @param $table
 * @param $field
 * @param null $fieldValue
 * @return string
 */
function installAjaxCheckboxDisplay($id, $table, $field, $fieldValue = null): string
{
    $lineId = $field.$id;
    $lineId = str_replace('.', '', $lineId); // fix JS bug (in admin layout)
    $data = 'data-table="' . $table . '"
			data-field="'.$field.'"
			data-line-id="' . $lineId . '"
			data-id="' . $id . '"
			data-value="' . $fieldValue . '"';

    // Decoration
    if ($fieldValue == 1) {
        $html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-on" aria-hidden="true"></i>';
    } else {
        $html = '<i id="' . $lineId . '" class="admin-single-icon fa fa-toggle-off" aria-hidden="true"></i>';
    }
    $html = '<a href="" class="ajax-request" ' . $data . '>' . $html . '</a>';

    // Install country's decoration
    $html .= ' &nbsp;';
    if ($fieldValue == 1) {
        $html .= '<a href="" id="install' . $id . '" class="ajax-request btn btn-xs btn-success text-white" ' . $data . '>';
		$html .= '<i class="fas fa-download"></i> ' . trans('admin.Installed');
		$html .= '</a>';
    } else {
        $html .= '<a href="" id="install' . $id . '" class="ajax-request btn btn-xs btn-light" ' . $data . '>';
		$html .= '<i class="fas fa-download"></i> ' . trans('admin.Install');
		$html .= '</a>';
    }

    return $html;
}

/**
 * Generate the Post's link from the Admin panel
 *
 * @param $post
 * @return string
 */
function getPostUrl($post): string
{
    $out = '';
    
    if (isset($post->latestPayment) && !empty($post->latestPayment)) {
        if (isset($post->latestPayment->package) && !empty($post->latestPayment->package)) {
			$info = '';
            if ($post->featured == 1) {
                $class = 'text-success';
            } else {
                $class = 'text-danger';
				$info = ' (' . trans('admin.Expired') . ')';
            }
            $out = ' <i class="fa fa-check-circle ' . $class . '"
                    data-bs-placement="bottom" data-bs-toggle="tooltip"
                    type="button" title="' . $post->latestPayment->package->short_name . $info . '">
                </i>';
        }
    }
	
    // Get URL
	if (!is_null($post) && isset($post->country_code, $post->title)) {
		$url = dmUrl($post->country_code, UrlGen::postPath($post));
		$out = linkStrLimit($url, $post->title, 35, 'target="_blank"') . $out;
	} else {
		$out = '--';
	}
	
    return $out;
}

/**
 * @param $entry
 * @param bool $withLink
 * @return string
 */
function getCountryFlag($entry, bool $withLink = false): string
{
	$out = '';
	
	if (isset($entry->country_code)) {
		$countryName = (isset($entry->country) && isset($entry->country->name)) ? $entry->country->name : null;
		$countryName = (!empty($countryName)) ? $countryName : $entry->country_code;
		
		$iconPath = 'images/flags/16/' . strtolower($entry->country_code) . '.png';
		if (file_exists(public_path($iconPath))) {
			$out = ($withLink) ? '<a href="' . dmUrl($entry->country_code, '/', true, true) . '" target="_blank">' : '';
			$out .= '<img src="' . url($iconPath) . getPictureVersion() . '" data-bs-toggle="tooltip" title="' . $countryName . '">';
			$out .= ($withLink) ? '</a>' : '';
			$out .= ' ';
		} else {
			$out .= $entry->country_code . ' ';
		}
	}
	
	return $out;
}

/**
 * Check if the Listing is verified
 *
 * @param $post
 * @return bool
 */
function isVerifiedPost($post): bool
{
	$post = (is_array($post)) ? Arr::toObject($post) : $post;
	
    if (!Arr::keyExists('email_verified_at', $post) || !Arr::keyExists('phone_verified_at', $post) || !Arr::keyExists('reviewed_at', $post)) {
        return false;
    }
    
    if (config('settings.single.listings_review_activation')) {
        $verified = (!empty($post->email_verified_at) && !empty($post->phone_verified_at) && !empty($post->reviewed_at));
    } else {
        $verified = (!empty($post->email_verified_at) && !empty($post->phone_verified_at));
    }
    
    return $verified;
}

/**
 * Check if the User is verified
 *
 * @param $user
 * @return bool
 */
function isVerifiedUser($user): bool
{
	$user = (is_array($user)) ? Arr::toObject($user) : $user;
	
	if (!Arr::keyExists('email_verified_at', $user) || !Arr::keyExists('phone_verified_at', $user)) {
		return false;
	}
    
    return (!empty($user->email_verified_at) && !empty($user->phone_verified_at));
}

/**
 * @return bool
 */
function userHasSuperAdminPermissions(): bool
{
	if (auth()->check()) {
		$permissions = \App\Models\Permission::getSuperAdminPermissions();
		
		// Remove the standard admin permission
		$permissions = collect($permissions)->reject(function ($value, $key) {
			return $value == 'dashboard-access';
		})->toArray();
		
		// Check if user has the super admin permissions
		if (auth()->user()->can($permissions)) {
			return true;
		}
	}
	
	return false;
}
