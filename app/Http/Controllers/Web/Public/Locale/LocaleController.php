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

namespace App\Http\Controllers\Web\Public\Locale;

use App\Helpers\Arr;
use App\Helpers\Cookie;
use App\Http\Controllers\Web\Public\FrontController;
use App\Http\Controllers\Web\Public\Locale\Traits\TranslateUrlTrait;

class LocaleController extends FrontController
{
	use TranslateUrlTrait;
	
	/**
	 * @param string $langCode
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function setLocale(string $langCode)
	{
		// Check if the selected Language Code is supported by the system
		if (!isAvailableLang($langCode)) {
			$message = t('language_not_supported', ['code' => $langCode]);
			flash($message)->error();
			
			return redirect()->back()->withHeaders(config('larapen.core.noCacheHeaders'));
		}
		
		// Check if Language Code can be saved into the session
		// and remove the Language Code session, if its cannot be saved.
		$langCanBeSaved = $this->checkIfLangCanBeSavedInSession($langCode);
		
		// Save the Language Code
		$this->saveLang($langCode, $langCanBeSaved);
		
		// After the Language Operation is done, ...
		
		// If the next path (URI) is filled (using the '?from=' parameter,
		// Then, redirect to this path
		if (request()->filled('from')) {
			$path = ltrim(request()->get('from'), '/');
			if (!empty($path) && !str_contains($path, '#')) {
				$url = request()->root() . '/' . $path;
				
				return redirect($url);
			}
		}
		
		// If the Country selection parameter is filled,
		// Redirect to the homepage with it (without the eventual 'from' parameter)
		// If not, redirect user to the previous page
		if (request()->filled('country')) {
			$queryString = '';
			$queryArray = request()->except(['from']);
			if (!empty($queryArray)) {
				$queryString = '?' . Arr::query($queryArray);
			}
			
			$nextUrl = request()->root() . '/' . $queryString;
			
			if (config('settings.app.auto_detect_language') == '2') {
				$nextUrl = $this->removeCountrySelectionParameter($nextUrl);
			}
			
			return redirect($nextUrl);
		} else {
			$previousUrl = url()->previous();
			if (config('settings.app.auto_detect_language') == '2') {
				$previousUrl = $this->removeCountrySelectionParameter($previousUrl);
			}
			$previousUrl = $this->translateUrl($previousUrl, $langCode);
			
			if (config('plugins.domainmapping.installed')) {
				$previousUrl = request()->root();
				
				$origParsedUrl = mb_parse_url(url()->previous());
				$parsedUrl = mb_parse_url(request()->root());
				
				if (isset($origParsedUrl['host'], $parsedUrl['host'])) {
					if ($origParsedUrl['host'] == $parsedUrl['host']) {
						$previousPath = isset($origParsedUrl['path']) && !empty($origParsedUrl['path'])
							? $origParsedUrl['path']
							: '';
						$previousPath = ltrim($previousPath, '/');
						$previousUrl = $previousUrl . '/' . $previousPath;
						$previousUrl = $this->translateUrl($previousUrl, $langCode, request()->root());
					}
				}
			}
			
			return redirect($previousUrl);
		}
	}
	
	/**
	 * Check if Language Code can be saved into the session
	 * and remove the Language Code session, if it's cannot be saved.
	 * ie:
	 *   - When, selected Language Code is equal to the website master Language Code
	 *   - Or when, the 'Website Country Language' detection option is activated
	 *     and the selected Language Code is equal to the Country's Language Code
	 *
	 * @param string $langCode
	 * @return bool
	 */
	private function checkIfLangCanBeSavedInSession(string $langCode): bool
	{
		$langCanBeSaved = true;
		if ($langCode == config('appLang.abbr')) {
			if (config('settings.app.auto_detect_language') == '2') {
				if ($langCode == config('lang.abbr')) {
					$langCanBeSaved = false;
				}
			} else {
				$langCanBeSaved = false;
			}
		}
		
		return $langCanBeSaved;
	}
	
	/**
	 * @param string $langCode
	 * @param bool $langCanBeSaved
	 * @return void
	 */
	private function saveLang(string $langCode, bool $langCanBeSaved)
	{
		if (config('larapen.core.storingUserSelectedLang') == 'cookie') {
			$this->saveLangInCookie($langCode, $langCanBeSaved);
		} else {
			$this->saveLangInSession($langCode, $langCanBeSaved);
		}
	}
	
	/**
	 * Save the Language Code in Cookie
	 *
	 * @param string $langCode
	 * @param bool $langCanBeSaved
	 * @return void
	 */
	private function saveLangInCookie(string $langCode, bool $langCanBeSaved)
	{
		if ($langCanBeSaved) {
			Cookie::set('langCode', $langCode);
		} else {
			Cookie::forget('langCode');
		}
	}
	
	/**
	 * Save the Language Code in Session
	 *
	 * @param string $langCode
	 * @param bool $langCanBeSaved
	 * @return void
	 */
	private function saveLangInSession(string $langCode, bool $langCanBeSaved)
	{
		if ($langCanBeSaved) {
			// Save the Language Code in Session
			session()->put('langCode', $langCode);
		} else {
			// Remove the Language Code from Session
			if (session()->has('langCode')) {
				session()->forget('langCode');
			}
		}
	}
	
	/**
	 * Remove the Country selection parameter from the URL
	 * (Helpful when the 'Website Country Language' detection option is activated)
	 * '(config('settings.app.auto_detect_language') == '2'))
	 *
	 * @param string|null $url
	 * @return string|string[]|null
	 */
	private function removeCountrySelectionParameter(string $url = null)
	{
		$parsedUrl = mb_parse_url($url);
		
		if (isset($parsedUrl['query'])) {
			parse_str($parsedUrl['query'], $queryArray);
			if (array_key_exists('country', $queryArray)) {
				$url = preg_replace('|\?.*|ui', '', $url);
				unset($queryArray['country']);
				if (!empty($queryArray)) {
					$queryString = '?' . Arr::query($queryArray);
					$url = $url . $queryString;
				}
			}
		}
		
		return $url;
	}
}
