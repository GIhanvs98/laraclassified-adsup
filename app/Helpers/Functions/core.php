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

/**
 * Check if a Model has translation fields
 *
 * @param $model
 * @return bool
 */
function isTranslatableModel($model): bool
{
	$isTranslatable = false;

	try {
		if (!($model instanceof \Illuminate\Database\Eloquent\Model)) {
			return false;
		}

		$isTranslatableModel = (
			property_exists($model, 'translatable')
			&& (
				isset($model->translatable)
				&& is_array($model->translatable)
				&& !empty($model->translatable)
			)
		);

		if ($isTranslatableModel) {
			$isTranslatable = true;
		}
	} catch (\Throwable $e) {
		return false;
	}

	return $isTranslatable;
}

/**
 * The App's version of Laravel view() function
 *
 * @param string $view
 * @param array $data
 * @param array $mergeData
 * @return \Illuminate\Contracts\View\View
 */
function appView(string $view, array $data = [], array $mergeData = []): \Illuminate\Contracts\View\View
{
	return view()->first([config('larapen.core.customizedViewPath') . $view, $view,], $data, $mergeData);
}

/**
 * Get View Content
 *
 * @param string $view
 * @param array $data
 * @param array $mergeData
 * @return string
 */
function getViewContent(string $view, array $data = [], array $mergeData = []): string
{
	if (view()->exists(config('larapen.core.customizedViewPath') . $view)) {
		$view = view(config('larapen.core.customizedViewPath') . $view, $data, $mergeData);
	} else {
		$view = view($view, $data, $mergeData);
	}

	return $view->render();
}

/**
 * Hide part of email addresses
 *
 * @param string|null $value
 * @param int $escapedChars
 * @return string|null
 */
function hidePartOfEmail(?string $value, int $escapedChars = 1): ?string
{
	$atPos = mb_stripos($value, '@');
	if ($atPos === false) {
		return $value;
	}

	$emailUsername = mb_substr($value, 0, $atPos);
	$emailDomain = mb_substr($value, ($atPos + 1));

	if (!empty($emailUsername) && !empty($emailDomain)) {
		$value = str($emailUsername)->mask('x', $escapedChars) . '@' . $emailDomain;
	}

	return $value;
}

/**
 * Default translator (e.g. en/global.php)
 *
 * @param string|null $key
 * @param array $replace
 * @param string $file
 * @param string|null $locale
 * @return array|\Illuminate\Contracts\Translation\Translator|string|null
 */
function t(string $key = null, array $replace = [], string $file = 'global', string $locale = null)
{
	if (is_null($locale)) {
		$locale = config('app.locale');
	}

	return trans($file . '.' . $key, $replace, $locale);
}

/**
 * Get default max file upload size (from PHP.ini)
 *
 * @return mixed
 */
function maxUploadSize()
{
	$maxUpload = (int) (ini_get('upload_max_filesize'));
	$maxPost = (int) (ini_get('post_max_size'));

	return min($maxUpload, $maxPost);
}

/**
 * Get max file upload size
 *
 * @return int|mixed
 */
function maxApplyFileUploadSize()
{
	$size = maxUploadSize();
	if ($size >= 5) {
		return 5;
	}

	return $size;
}

/**
 * Escape JSON string
 *
 * Escape this:
 * \b  Backspace (ascii code 08)
 * \f  Form feed (ascii code 0C)
 * \n  New line
 * \r  Carriage return
 * \t  Tab
 * \"  Double quote
 * \\  Backslash caracter
 *
 * @param string|null $value
 * @return string|null
 */
function escapeJsonString(?string $value): ?string
{
	// list from www.json.org: (\b backspace, \f formfeed)
	$escapers = ["\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c"];
	$replacements = ["\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b"];
	$value = str_replace($escapers, $replacements, trim($value));

	return trim($value);
}

/**
 * @param string|null $defaultIp
 * @return string
 */
function getIp(?string $defaultIp = ''): string
{
	return \App\Helpers\Ip::get($defaultIp);
}

/**
 * @return string
 */
function getScheme(): string
{
	if (isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], ['on', 1])) {
		$protocol = 'https';
	} else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
		$protocol = 'https';
	} else if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}

	return $protocol . '://';
}


/**
 * Get host (domain with subdomain)
 *
 * @param string|null $url
 * @return array|mixed|string
 */
function getHost(string $url = null)
{
	if (!empty($url)) {
		$host = parse_url($url, PHP_URL_HOST);
	} else {
		$host = (trim(request()->server('HTTP_HOST')) != '') ? request()->server('HTTP_HOST') : ($_SERVER['HTTP_HOST'] ?? '');
	}

	if ($host == '') {
		$host = parse_url(url()->current(), PHP_URL_HOST);
	}

	return $host;
}

/**
 * Get domain (host without subdomain)
 *
 * @param string|null $url
 * @return string
 */
function getDomain(string $url = null): string
{
	if (!empty($url)) {
		$host = parse_url($url, PHP_URL_HOST);
	} else {
		$host = getHost();
	}

	$tmp = explode('.', $host);
	if (count($tmp) > 2) {
		$itemsToKeep = count($tmp) - 2;
		$tlds = config('tlds');
		if (isset($tmp[$itemsToKeep]) && isset($tlds[$tmp[$itemsToKeep]])) {
			$itemsToKeep = $itemsToKeep - 1;
		}
		for ($i = 0; $i < $itemsToKeep; $i++) {
			Arr::forget($tmp, $i);
		}
		$domain = implode('.', $tmp);
	} else {
		$domain = @implode('.', $tmp);
	}

	return $domain;
}

/**
 * Get subdomain name
 *
 * NOTE:
 * The subdomains of the fetched subdomain are not retrieved
 * Example: xxx.yyy.zzz.foo.com, only "xxx" will be retrieved
 *
 * @return string
 */
function getSubDomainName(): string
{
	$host = getHost();

	return (substr_count($host, '.') > 1) ? trim(current(explode('.', $host))) : '';
}

/**
 * @return string
 */
function getCookieDomain(): string
{
	$host = getHost();
	$array = mb_parse_url($host);

	return (is_array($array) && isset($array['path']) && !empty($array['path']))
		? $array['path']
		: $host;
}

/**
 * @return string
 */
function getCountryCodeRoutePattern(): string
{
	// Country Code Pattern
	$countryCodePattern = implode('|', array_map('strtolower', array_keys(getCountries())));
	$countryCodePattern = !empty($countryCodePattern) ? $countryCodePattern : 'us';
	/*
	 * NOTE:
	 * '(?i:foo)' : Make 'foo' case-insensitive
	 */
	return '(?i:' . $countryCodePattern . ')';
}

/**
 * @return bool
 */
function doesCountriesPageCanBeHomepage(): bool
{
	return (
		file_exists(storage_path('framework/plugins/domainmapping'))
		&& (env('DM_COUNTRIES_PAGE_AS_HOMEPAGE') == true)
		&& (getHost() == getHost(env('APP_URL')))
	);
}

/**
 * @return bool
 */
function doesCountriesPageCanBeLinkedToTheHomepage(): bool
{
	return (
		file_exists(storage_path('framework/plugins/domainmapping'))
		&& (env('DM_COUNTRIES_PAGE_AS_HOMEPAGE') == true)
		&& (getHost() != getHost(env('APP_URL')))
	);
}

/**
 * Generate a URL with query string for the application.
 *
 * Assumes that you want a URL with a querystring rather than route params
 * (which is what the default url() helper does)
 *
 * @param string|null $path
 * @param array|null $queryArray
 * @param $secure
 * @param bool $localized
 * @return string
 */
function qsUrl(string $path = null, ?array $queryArray = [], $secure = null, bool $localized = true): string
{
	$url = getUrlWithoutQuery($path, $secure);

	// $queryArray = array_merge(getUrlQuery($path), $queryArray);

	if (config('plugins.domainmapping.installed')) {
		if (isset($queryArray['country'])) {
			unset($queryArray['country']);
		}
		$queryArray = array_filter($queryArray, function ($v, $k) {
			if ($k == 'distance') {
				return !empty($v) || $v == 0;
			} else {
				return !empty($v);
			}
		}, ARRAY_FILTER_USE_BOTH);
	}

	if (!empty($queryArray)) {
		$url = $url . '?' . Arr::query($queryArray);
	}

	return $url;
}

/**
 * Get the URL (no query string) for the given URL or for the request
 *
 * @param string|null $url
 * @param bool|null $secure
 * @return string
 */
function getUrlWithoutQuery(?string $url, bool $secure = null): string
{
	if (empty($url)) {
		$url = request()->fullUrl();
		if (empty($url))
			return '';
	} else {
		// Accepts URI|Path as URL
		$url = url()->to($url, [], $secure);
	}

	$url = preg_replace('/\?.*/ui', '', $url);

	return is_string($url) ? rtrim($url, '/') : '';
}

/**
 * Get query string from a given URL
 * NOTE: Possibility to except some query
 *
 * @param string|null $url
 * @param array|string|null $except
 * @return array
 */
function getUrlQuery(?string $url, $except = null): array
{
	if (empty($url)) {
		$url = request()->fullUrl();
		if (empty($url))
			return [];
	}

	$queryArray = [];

	$parsedUrl = mb_parse_url($url);
	if (isset($parsedUrl['query'])) {
		mb_parse_str($parsedUrl['query'], $queryArray);

		if (!empty($except)) {
			if (is_array($except)) {
				foreach ($except as $item) {
					if (isset($queryArray[$item])) {
						unset($queryArray[$item]);
					}
				}
			}
			if (is_string($except) || is_numeric($except)) {
				if (isset($queryArray[$except])) {
					unset($queryArray[$except]);
				}
			}
		}
	}

	return $queryArray;
}

/**
 * @info: Depreciated - This function will be removed in the next updated
 *
 * @param string|null $path
 * @param array|null $attributes
 * @param null $locale
 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function lurl(string $path = null, ?array $attributes = [], $locale = null)
{
	return url($path);
}

/**
 * @info: Depreciated - This function will be removed in the next updated
 * @param $country
 * @param string|null $path
 * @param bool $forceCountry
 * @param bool $forceLocale
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function localUrl($country, ?string $path = '/', bool $forceCountry = false, bool $forceLocale = false)
{
	return dmUrl($country, $path, $forceCountry, $forceLocale);
}

/**
 * Get URL (based on Country Domain) related to the given country (or country code)
 * This is the url() function to match countries domains
 *
 * @param string|\Illuminate\Support\Collection $country
 * @param string|null $path
 * @param bool $forceCountry
 * @param bool $forceLocale
 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function dmUrl($country, ?string $path = '/', bool $forceCountry = false, bool $forceLocale = false)
{
	if (empty($path)) {
		$path = '/';
	}

	$country = getValidCountry($country);
	if (empty($country)) {
		return url($path);
	}

	// Clear the path
	$path = ltrim($path, '/');

	// Get the country main language code
	$langCode = getCountryMainLangCode($country);

	// Get the country main language path
	$langPath = '';
	if ($forceLocale) {
		if (!empty($langCode)) {
			$parseUrl = mb_parse_url(url($path));
			if (!isset($parseUrl['path']) || ($parseUrl['path'] == '/')) {
				$langPath = '/locale/' . $langCode;
			}
			if (isFromUrlAlwaysContainingCountryCode($path)) {
				$langPath = '/' . $langCode;
			}
		}
	}

	// Get the country domain data from the Domain Mapping plugin,
	// And get a new URL related to domain, country language & given path
	$domain = collect((array) config('domains'))->firstWhere('country_code', $country->get('code'));
	if (isset($domain['url']) && !empty($domain['url'])) {
		$path = preg_replace('#' . $country->get('code') . '/#ui', '', $path, 1);

		$url = rtrim($domain['url'], '/') . $langPath;
		$url = $url . ((!empty($path)) ? '/' . $path : '');
	} else {
		$url = rtrim(env('APP_URL', ''), '/') . $langPath;
		$url = $url . ((!empty($path)) ? '/' . $path : '');
		if ($forceCountry) {
			$url = $url . ('?country=' . $country->get('code'));
		}
	}

	return $url;
}

/**
 * Get Valid Country's Object (as Laravel Collection)
 *
 * @param string|\Illuminate\Support\Collection $country
 * @return \Illuminate\Support\Collection|null
 */
function getValidCountry($country): ?\Illuminate\Support\Collection
{
	// If given country value is a string & having 2 characters (like country code),
	// Get the country collection by the country code.
	if (is_string($country)) {
		if (strlen($country) == 2) {
			$country = \App\Helpers\Localization\Country::getCountryInfo($country);
			if ($country->isEmpty() || !$country->has('code')) {
				return null;
			}
		} else {
			return null;
		}
	}

	// Country collection is required to continue
	if (!($country instanceof \Illuminate\Support\Collection)) {
		return null;
	}

	// Country collection code is required to continue
	if (!$country->has('code')) {
		return null;
	}

	return $country;
}

/**
 * Get Country Main Language Code
 *
 * @param string|\Illuminate\Support\Collection $country
 * @return string|null
 */
function getCountryMainLangCode($country): ?string
{
	$country = getValidCountry($country);
	if (empty($country)) {
		return null;
	}

	// Get the country main language code
	$langCode = null;
	if ($country->has('lang') && $country->get('lang')->has('abbr')) {
		$langCode = $country->get('lang')->get('abbr');
	} else {
		if ($country->has('languages')) {
			$countryLang = \App\Helpers\Localization\Country::getLangFromCountry($country->get('languages'));
			if ($countryLang->has('abbr')) {
				$langCode = $countryLang->get('abbr');
			}
		} else {
			// From XML Sitemaps
			if ($country->has('locale')) {
				$langCode = $country->get('locale');
			}
		}
	}

	return $langCode;
}

/**
 * If the Domain Mapping plugin is installed, apply its configs.
 * NOTE: Don't apply them if the session is shared.
 *
 * @param $countryCode
 */
function applyDomainMappingConfig($countryCode)
{
	if (empty($countryCode)) {
		return;
	}

	if (config('plugins.domainmapping.installed')) {
		/*
		 * When the session is shared, the domains name and logo columns are disabled.
		 * The dashboard per country feature is also disabled.
		 * So, it is recommended to access to the Admin panel through the main URL from the /.env file (i.e. APP_URL/admin)
		 */
		if (!config('settings.domainmapping.share_session')) {
			$domain = collect((array) config('domains'))->firstWhere('country_code', $countryCode);
			if (!empty($domain)) {
				if (isset($domain['url']) && !empty($domain['url'])) {
					//\URL::forceRootUrl($domain['url']);
				}
			}
		}
	}
}

function isFromAdminPanel($url = null): bool
{
	return isAdminPanel($url);
}

/**
 * Check if user is located in the Admin panel
 * NOTE: Please see the provider of the package: lab404/laravel-impersonate
 *
 * @param string|null $url
 * @return bool
 */
function isAdminPanel(string $url = null): bool
{
	if (empty($url)) {
		$isValid = (
			request()->segment(1) == admin_uri()
			|| request()->segment(1) == 'impersonate'
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), '\Admin\\')
		);
	} else {
		try {
			$urlPath = '/' . ltrim(parse_url($url, PHP_URL_PATH), '/');
			$adminUri = '/' . ltrim(admin_uri(), '/');

			$isValid = (
				str_starts_with($urlPath, $adminUri)
				|| str_starts_with($urlPath, '/impersonate')
			);
		} catch (\Throwable $e) {
			$isValid = false;
		}
	}

	return $isValid;
}

/**
 * Check local environment
 *
 * @param string|null $url
 * @return bool
 */
function isLocalEnv(string $url = null): bool
{
	if (empty($url)) {
		$url = env('APP_URL');
	}

	return (
		str_contains($url, '127.0.0.1')
		|| str_contains($url, '::1')
		|| (!str_contains($url, '.'))
		|| str_ends_with(getDomain($url), '.local')
		|| str_ends_with(getDomain($url), '.localhost')
	);
}

/**
 * Check dev environment
 *
 * @param string|null $url
 * @return bool
 */
function isDevEnv(string $url = null): bool
{
	if (empty($url)) {
		$url = env('APP_URL');
	}

	$domain = getDomain($url);

	return (
		str_contains($domain, 'bedigit.local')
		|| str_contains($domain, 'laraclassifier.local')
	);
}

/**
 * Check demo environment
 *
 * @param string|null $url
 * @return bool
 */
function isDemoEnv(string $url = null): bool
{
	if (empty($url)) {
		$url = env('APP_URL');
	}

	return (
		getDomain($url) == config('larapen.core.demo.domain')
		|| in_array(getHost($url), (array) config('larapen.core.demo.hosts'))
	);
}

/**
 * Check the demo website domain
 *
 * @param string|null $url
 * @return bool
 */
function isDemoDomain(string $url = null): bool
{
	$isDemoDomain = isDemoEnv($url);

	if (!$isDemoDomain) {
		return false;
	}

	if (auth()->check()) {
		if (
			auth()->user()->can(\App\Models\Permission::getStaffPermissions())
			&& md5(auth()->user()->id) == 'c4ca4238a0b923820dcc509a6f75849b'
		) {
			$isDemoDomain = false;
		}
	}

	return $isDemoDomain;
}

/**
 * Human-readable file size
 *
 * @param $bytes
 * @param int $decimals
 * @param string $system (metric OR binary)
 * @return string
 */
function readableBytes($bytes, int $decimals = 2, string $system = 'binary')
{
	if (!is_numeric($bytes)) {
		return $bytes;
	}

	$mod = ($system === 'binary') ? 1024 : 1000;

	$units = [
		'binary' => [
			'B',
			'KiB',
			'MiB',
			'GiB',
			'TiB',
			'PiB',
			'EiB',
			'ZiB',
			'YiB',
		],
		'metric' => [
			'B',
			'kB',
			'MB',
			'GB',
			'TB',
			'PB',
			'EB',
			'ZB',
			'YB',
		],
	];

	$factor = floor((strlen($bytes) - 1) / 3);
	$unit = $units[$system][$factor] ?? $units['binary'][$factor];
	$bytes = $bytes / pow($mod, $factor);

	$bytes = \App\Helpers\Number::format($bytes, $decimals);

	return $bytes . $unit;
}

/**
 * Get the Country Code from URI Path
 *
 * @return string|null
 */
function getCountryCodeFromPath(): ?string
{
	$countryCode = null;

	// With these URLs, the language code and the country code can be available in the segments
	// (If the "Multi-countries URLs Optimization" is enabled)
	if (isFromUrlThatCanContainCountryCode()) {
		$countryCode = request()->segment(1);
	}

	// With these URLs, the language code and the country code are available in the segments
	if (isFromUrlAlwaysContainingCountryCode()) {
		$countryCode = request()->segment(2);
	}

	return $countryCode;
}

/**
 * Check if user is coming from a URL that can contain the country code
 * With these URLs, the language code and the country code can be available in the segments
 * (If the "Multi-countries URLs Optimization" is enabled)
 *
 * @return bool
 */
function isFromUrlThatCanContainCountryCode(): bool
{
	if (config('settings.seo.multi_countries_urls')) {
		if (
			str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'SearchController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'CategoryController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'CityController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'UserController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'TagController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'CompanyController')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'SitemapController')
		) {
			return true;
		}
	}

	return false;
}

/**
 * Check if called page can always have the country code
 * With these URLs, the language code and the country code are available in the segments
 *
 * @param string|null $url
 * @return bool
 */
function isFromUrlAlwaysContainingCountryCode(string $url = null): bool
{
	if (empty($url)) {
		$isValid = (
			str_ends_with(request()->url(), '.xml')
			|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'SitemapsController')
		);
	} else {
		$isValid = (str_ends_with($url, '.xml'));
	}

	return $isValid;
}

/**
 * Check if value is uploaded file data
 *
 * @param $value
 * @return bool
 */
function isUploadedFile($value): bool
{
	if (
		($value instanceof \Illuminate\Http\UploadedFile)
		|| (is_string($value) && str_starts_with($value, 'data:image'))
	) {
		return true;
	}

	return false;
}

function fileIsUploaded($value): bool
{
	return isUploadedFile($value);
}

/**
 * Get the uploaded file mime type
 *
 * @param $value
 * @return string|null
 */
function getUploadedFileMimeType($value): ?string
{
	$mimeType = null;

	if (!is_string($value)) {
		if ($value instanceof \Illuminate\Http\UploadedFile) {
			$mimeType = $value->getMimeType();
		}
	} else {
		if (str_starts_with($value, 'data:image')) {
			try {
				$mimeType = mime_content_type($value);
			} catch (\Throwable $e) {
			}
		}

		if (empty($mimeType)) {
			$mimeType = 'image/jpeg';
		}
	}

	return strtolower($mimeType);
}

/**
 * Get the uploaded file extension
 *
 * @param $value
 * @return string|null
 */
function getUploadedFileExtension($value): ?string
{
	$extension = null;

	if (!is_string($value)) {
		if ($value instanceof \Illuminate\Http\UploadedFile) {
			$extension = $value->getClientOriginalExtension();
		}
	} else {
		if (str_starts_with($value, 'data:image')) {
			$matches = [];
			preg_match('#data:image/([^;]+);base64#', $value, $matches);
			$extension = (isset($matches[1]) && !empty($matches[1])) ? $matches[1] : 'png';
		} else {
			$extension = getExtension($value);
		}
	}

	return strtolower($extension);
}

/**
 * Get file extension
 *
 * @param string|null $filename
 * @return false|mixed|string
 */
function getExtension(?string $filename)
{
	$tmp = explode('?', $filename);
	$tmp = explode('.', current($tmp));

	return end($tmp);
}

/**
 * Transform Description column before displaying it
 *
 * @param $string
 * @return mixed|string
 */
function transformDescription($string)
{
	if (config('settings.single.wysiwyg_editor') != 'none') {

		try {
			$string = \Mews\Purifier\Facades\Purifier::clean($string);
		} catch (\Throwable $e) {
			// Nothing.
		}
		$string = createAutoLink($string);

	} else {
		$string = nl2br(createAutoLink(strCleaner($string)));
	}

	return $string;
}

/**
 * String strip
 *
 * @param string|null $string
 * @return string|null
 */
function str_strip(?string $string): ?string
{
	return trim(preg_replace('/\s\s+/u', ' ', $string));
}

/**
 * String cleaner
 *
 * @param string|null $string
 * @return string|null
 */
function strCleaner(?string $string): ?string
{
	$string = strip_tags($string, '<br><br/>');
	$string = str_replace(['<br>', '<br/>', '<br />'], "\n", $string);
	$string = preg_replace("/[\r\n]+/", "\n", $string);
	/*
	   Remove 4(+)-byte characters from a UTF-8 string
	   It seems like MySQL does not support characters with more than 3 bytes in its default UTF-8 charset.
	   NOTE: you should not just strip, but replace with replacement character U+FFFD to avoid unicode attacks, mostly XSS:
	   http://unicode.org/reports/tr36/#Deletion_of_Noncharacters
	   */
	$string = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $string);

	return mb_ucfirst(trim($string));
}

/**
 * String cleaner (Lite version)
 *
 * @param string|null $string
 * @return array|string|string[]|null
 */
function strCleanerLite(?string $string)
{
	$string = strip_tags($string);
	$string = html_entity_decode($string);
	$string = strip_tags($string);
	$string = preg_replace('/[\'"]*(<|>)[\'"]*/us', '', $string);
	$string = trim($string);

	/*
	   Remove non-breaking spaces
	   In HTML, the common non-breaking space, which is the same width as the ordinary space character, is encoded as &nbsp; or &#160;.
	   In Unicode, it is encoded as U+00A0.
	   https://en.wikipedia.org/wiki/Non-breaking_space
	   https://graphemica.com/00A0
	   */

	return preg_replace('~\x{00a0}~siu', '', $string);
}

/**
 * Title cleaner
 *
 * @param string|null $string
 * @return array|string|string[]|null
 * @todo: Code not tested. Test it!
 *
 */
function titleCleaner(?string $string)
{
	$string = strip_tags($string);
	$string = html_entity_decode($string);
	$string = str_replace('º', '', $string);
	$string = str_replace('ª', '', $string);

	/*
	   Match a single character not present in the list below
	   [^\p{L}\p{M}\p{Z}\p{N}\p{Sc}\%\'\"!?¿¡-]
	   \p{L} matches any kind of letter from any language
	   \p{M} matches a character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.)
	   \p{Z} matches any kind of whitespace or invisible separator
	   \p{N} matches any kind of numeric character in any script
	   \p{Sc} matches any currency sign
	   \% matches the character % literally (case sensitive)
	   \' matches the character ' literally (case sensitive)
	   \" matches the character " literally (case sensitive)
	   !?¿¡- matches a single character in the list !?¿¡- (case sensitive)
	   
	   Global pattern flags
	   g modifier: global. All matches (don't return after first match)
	   m modifier: multi line. Causes ^ and $ to match the begin/end of each line (not only begin/end of string)
	   */
	$string = preg_replace('/[^\p{L}\p{M}\p{Z}\p{N}\p{Sc}\%\'\"\!\?¿¡\-]/u', ' ', $string);

	$string = preg_replace('/[\'"]*(<|>)[\'"]*/us', '', $string);
	$string = str_replace(' ', ' ', $string); // do NOT remove, first is NOT blank space.
	$string = str_replace('️', ' ', $string); // do NOT remove, there is a ghost.
	$string = preg_replace('/-{2,}/', '-', $string);
	$string = preg_replace('/"{2,}/', '"', $string);
	$string = preg_replace("/'{2,}/", "'", $string);
	$string = preg_replace('/!{2,}/', '!', $string);
	$string = preg_replace("/[\?]+/", "?", $string);
	$string = preg_replace("/[%]+/", "%", $string);
	$string = str_replace('- -', ' - ', $string);
	$string = str_replace('! !', ' ! ', $string);
	$string = str_replace('? ?', ' ? ', $string);
	$string = rtrim($string, '-');
	$string = ltrim($string, '-');
	$string = trim(preg_replace('/\s+/', ' ', $string)); // strip blank spaces, tabs
	$string = trim($string);

	/*
	   Remove non-breaking spaces
	   In HTML, the common non-breaking space, which is the same width as the ordinary space character, is encoded as &nbsp; or &#160;.
	   In Unicode, it is encoded as U+00A0.
	   https://en.wikipedia.org/wiki/Non-breaking_space
	   https://graphemica.com/00A0
	   */

	return preg_replace('~\x{00a0}~siu', '', $string);
}

/**
 * Tags Cleaner
 * Prevent problem with the #hashtags when they are only numeric
 *
 * @param $tagString
 * @param bool $forceArrayReturn
 * @return array|string|null
 */
function tagCleaner($tagString, bool $forceArrayReturn = false)
{
	$limit = (int) config('settings.single.tags_limit', 15);

	if (!is_array($tagString) && !is_string($tagString)) {
		return $forceArrayReturn ? [] : null;
	}

	$arrayExpected = false;

	if (is_array($tagString)) {
		$tagsArray = $tagString;
		$arrayExpected = true;
	} else {
		$tagsArray = preg_split('|[:,;#_\|\n\t]+|ui', $tagString);
	}

	$tags = [];
	$i = 0;
	foreach ($tagsArray as $tag) {
		$tag = strCleanerLite($tag);

		// Remove all tags (simultaneously) staring and ending by a number
		$tag = preg_replace('/\b\d+\b/ui', '', $tag);

		// Remove special characters
		$tag = str_replace([':', ',', ';', '_', '\\', '/', '|', '+'], '', $tag);

		// Change the tag case (lowercase)
		$tag = mb_strtolower(trim($tag));

		if ($tag != '') {
			if (mb_strlen($tag) > 1) {
				if ($i <= $limit) {
					$tags[] = $tag;
				}
				$i++;
			}
		}
	}
	$tags = array_unique($tags);

	if ($arrayExpected || $forceArrayReturn) {
		return $tags;
	}

	return !empty($tags) ? implode(',', $tags) : null;
}

function tagRegexPattern(): string
{
	/*
	 * Tags (Only allow letters, numbers, spaces and ',;_-' symbols)
	 *
	 * Explanation:
	 * [] 	=> character class definition
	 * p{L} => matches any kind of letter character from any language
	 * p{N} => matches any kind of numeric character
	 * _- 	=> matches underscore and hyphen
	 * + 	=> Quantifier — Matches between one to unlimited times (greedy)
	 * /u 	=> Unicode modifier. Pattern strings are treated as UTF-16. Also causes escape sequences to match unicode characters
	 */
	return '/^[\p{L}\p{N} ,;_-]+$/u';
}

/**
 * Only numeric string cleaner
 *
 * @param string|null $string
 * @return string|null
 */
function onlyNumCleaner(?string $string): ?string
{
	$tmpString = preg_replace('/\d/u', '', strip_tags($string));
	if ($tmpString == '') {
		$string = null;
	}

	return $string;
}

/**
 * Extract emails from string, and get the first
 *
 * @param string|null $string
 * @return string|null
 */
function extractEmailAddress(?string $string): ?string
{
	$tmp = [];
	preg_match_all('/([a-z0-9\-\._%\+]+@[a-z0-9\-\.]+\.[a-z]{2,4}\b)/i', $string, $tmp);
	$emails = (isset($tmp[1])) ? $tmp[1] : [];
	$email = head($emails);
	if ($email == '') {
		$tmp = [];
		preg_match('/[a-z0-9\-_]+(\.[a-z0-9\-_]+)*@[a-z0-9\-]+(\.[a-z0-9\-]+)*(\.[a-z]{2,3})/i', $string, $tmp);
		$email = (isset($tmp[0])) ? trim($tmp[0]) : '';
		if ($email == '') {
			$tmp = [];
			preg_match('/[a-z0-9\-\._%\+]+@[a-z0-9\-\.]+\.[a-z]{2,4}\b/i', $string, $tmp);
			$email = (isset($tmp[0])) ? trim($tmp[0]) : '';
		}
	}

	return strtolower($email);
}

/**
 * Return an array of all supported Languages
 *
 * @return array
 */
function getSupportedLanguages(): array
{
	$supportedLanguages = [];

	$cacheExpiration = (int) config('settings.optimization.cache_expiration', 86400);

	// Get supported languages from database
	try {
		// Get all DB Languages
		$activeLanguages = cache()->remember('languages.active.array', $cacheExpiration, function () {
			try {
				$activeLanguages = \App\Models\Language::where('active', 1)->orderBy('lft', 'ASC')->get()->toArray();
			} catch (\Throwable $e) {
				$activeLanguages = \App\Models\Language::where('active', 1)->get()->toArray();
			}

			return $activeLanguages;
		});

		if (count($activeLanguages)) {
			foreach ($activeLanguages as $key => $lang) {
				$lang['regional'] = $lang['locale'];
				$supportedLanguages[$lang['abbr']] = $lang;
			}
		}
	} catch (\Throwable $e) {
		/*
		 * Database or tables don't exist.
		 * The script will display an error or will start the installation.
		 * Please don't change anything here.
		 */
	}

	return $supportedLanguages;
}

/**
 * Check if language code is available
 *
 * @param string|null $abbr
 * @return bool
 */
function isAvailableLang(?string $abbr): bool
{
	$cacheExpiration = (int) config('settings.optimization.cache_expiration', 86400);
	$lang = cache()->remember('language.' . $abbr, $cacheExpiration, function () use ($abbr) {
		return \App\Models\Language::where('abbr', $abbr)->first();
	});

	return (!empty($lang));
}

/**
 * @param string|null $url
 * @return array|string|string[]|null
 */
function getHostByUrl(?string $url)
{
	if (empty($url)) {
		return null;
	}

	// in case scheme relative URI is passed, e.g., //www.google.com/
	$url = trim($url, '/');

	// If scheme not included, prepend it
	if (!preg_match('#^http(s)?://#', $url)) {
		$url = 'http' . '://' . $url;
	}

	$urlParts = parse_url($url);

	// remove www
	return preg_replace('/^www\./', '', $urlParts['host']);
}

/**
 * Add rel="nofollow" to links
 *
 * @param string|null $html
 * @param string|null $skip
 * @return array|string|string[]|null
 */
function noFollow(?string $html, string $skip = null)
{
	$callback = function ($mach) use ($skip) {
		return (!($skip && str_contains($mach[1], $skip)) && !str_contains($mach[1], 'rel='))
			? $mach[1] . ' rel="nofollow">'
			: $mach[0];
	};

	return preg_replace_callback("#(<a[^>]+?)>#is", $callback, $html);
}

/**
 * Create auto-links for URLs in string
 *
 * @param string|null $str
 * @param array $attributes
 * @return array|string|string[]
 */
function createAutoLink(?string $str, array $attributes = [])
{
	// Transform URL to HTML link
	$attrs = '';
	foreach ($attributes as $attribute => $value) {
		$attrs .= " {$attribute}=\"{$value}\"";
	}

	$str = ' ' . $str;
	$str = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a rel="nofollow" href="$2"' . $attrs . ' target="_blank">$2</a>', $str);
	$str = substr($str, 1);

	// Add rel="nofollow" to links
	$httpHost = $_SERVER['HTTP_HOST'] ?? request()->server('HTTP_HOST');
	$parse = parse_url('http' . '://' . $httpHost);
	$str = noFollow($str, $parse['host']);

	// Find and attach target="_blank" to all href links from text
	return openLinksInNewWindow($str);
}

/**
 * Find and attach target="_blank" to all href links from text
 *
 * @param string|null $content
 * @return array|string|string[]
 */
function openLinksInNewWindow(?string $content)
{
	// Find all links
	preg_match_all('/<a ((?!target)[^>])+?>/ui', $content, $hrefMatches);

	// Loop only first array to modify links
	if (is_array($hrefMatches) && isset($hrefMatches[0])) {
		foreach ($hrefMatches[0] as $key => $value) {
			// Take orig link
			$origLink = $value;

			// Does it have target="_blank"
			if (!preg_match('/target="_blank"/ui', $origLink)) {
				// Add target = "_blank"
				$newLink = preg_replace("/<a(.*?)>/ui", "<a$1 target=\"_blank\">", $origLink);

				// Replace old link in content with new link
				$content = str_replace($origLink, $newLink, $content);
			}
		}
	}

	return $content;
}

/**
 * Add target=_blank to outside links
 *
 * @param string|null $content
 * @return null|string|string[]
 */
function openOutsideLinksInNewWindow(?string $content)
{
	// Remove existing "target" attribute
	$content = preg_replace('# target=[\'"]?[^\'"]*[\'"]?#ui', '', $content);

	// Add target=_blank to outside links
	$pattern = '#(<a\\b[^<>]*href=[\'"]?http[^<>]+)>#ui';
	$replace = '$1 target="_blank">';

	return preg_replace($pattern, $replace, $content);
}

/**
 * Check tld is a valid tld
 *
 * @param string|null $url
 * @return bool|int
 */
function checkTld(?string $url)
{
	if (empty($url)) {
		return null;
	}

	$parsedUrl = parse_url($url);
	if ($parsedUrl === false) {
		return false;
	}

	$tldArray = config('tlds');
	$patten = implode('|', array_keys($tldArray));

	return preg_match('/\.(' . $patten . ')$/i', $parsedUrl['host']);
}

/**
 * Function to convert hex value to rgb array
 *
 * @param string|null $colour
 * @return array|bool
 *
 * @todo: improve this function
 */
function hex2rgb(?string $colour)
{
	if ($colour[0] == '#') {
		$colour = substr($colour, 1);
	}
	if (strlen($colour) == 6) {
		[$r, $g, $b] = [$colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]];
	} else if (strlen($colour) == 3) {
		[$r, $g, $b] = [$colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]];
	} else {
		return false;
	}
	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);

	return ['r' => $r, 'g' => $g, 'b' => $b];
}

/**
 * Convert hexdec color string to rgb(a) string
 *
 * @param $color
 * @param bool $opacity
 * @return string
 *
 * @todo: improve this function
 */
function hex2rgba($color, bool $opacity = false): string
{
	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if (empty($color)) {
		return $default;
	}

	//Sanitize $color if "#" is provided
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
	} else if (strlen($color) == 3) {
		$hex = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb = array_map('hexdec', $hex);

	//Check if opacity is set(rgba or rgb)
	if ($opacity) {
		if (abs($opacity) > 1) {
			$opacity = 1.0;
		}
		$output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode(",", $rgb) . ')';
	}

	// Return rgb(a) color string
	return $output;
}

/**
 * ucfirst() function for multibyte character encodings
 *
 * @param string|null $string
 * @param string $encoding
 * @return string|null
 */
function mb_ucfirst(?string $string, string $encoding = 'utf-8'): ?string
{
	if (empty($string)) {
		return null;
	}

	$strLen = mb_strlen($string, $encoding);
	$firstChar = mb_substr($string, 0, 1, $encoding);
	$then = mb_substr($string, 1, $strLen - 1, $encoding);

	return mb_strtoupper($firstChar, $encoding) . $then;
}

/**
 * ucwords() function for multibyte character encodings
 *
 * @param string|null $string
 * @param string $encoding
 * @return string|null
 */
function mb_ucwords(?string $string, string $encoding = 'utf-8'): ?string
{
	if (empty($string)) {
		return null;
	}

	$tab = [];

	// Split the phrase by any number of space characters, which include " ", \r, \t, \n and \f
	$words = preg_split('/\s+/ui', $string);
	if (!empty($words)) {
		foreach ($words as $key => $word) {
			$tab[$key] = mb_ucfirst($word, $encoding);
		}
	}

	return (!empty($tab)) ? implode(' ', $tab) : null;
}

/**
 * parse_url() function for multi-bytes character encodings
 *
 * @param string|null $url
 * @param int $component
 * @return mixed
 */
function mb_parse_url(?string $url, int $component = -1)
{
	$callback = function ($matches) {
		return urlencode($matches[0]);
	};
	$encodedUrl = preg_replace_callback('%[^:/@?&=#]+%usD', $callback, $url);

	if (empty($encodedUrl)) {
		return null;
	}

	$parts = parse_url($encodedUrl, $component);

	if ($parts === false) {
		throw new \InvalidArgumentException('Malformed URL: ' . $url);
	}

	if (is_array($parts) && count($parts) > 0) {
		foreach ($parts as $name => $value) {
			$parts[$name] = urldecode($value);
		}
	}

	return $parts;
}

/**
 * Friendly UTF-8 URL for all languages
 *
 * @param string|null $string
 * @param string $separator
 * @return array|string|string[]|null
 */
function slugify(?string $string, string $separator = '-')
{
	// Remove accents using WordPress API method.
	$string = remove_accents($string);

	// Slug
	$string = mb_strtolower($string);
	$string = @trim($string);
	$replace = "/(\\s|\\" . $separator . ")+/mu";
	$subst = $separator;
	$string = preg_replace($replace, $subst, $string);

	// Remove unwanted punctuation, convert some to '-'
	$puncTable = [
		// remove
		"'" => '',
		'"' => '',
		'`' => '',
		'=' => '',
		'+' => '',
		'*' => '',
		'&' => '',
		'^' => '',
		'' => '',
		'%' => '',
		'$' => '',
		'#' => '',
		'@' => '',
		'!' => '',
		'<' => '',
		'>' => '',
		'?' => '',
		// convert to minus
		'[' => '-',
		']' => '-',
		'{' => '-',
		'}' => '-',
		'(' => '-',
		')' => '-',
		' ' => '-',
		',' => '-',
		';' => '-',
		':' => '-',
		'/' => '-',
		'|' => '-',
		'\\' => '-',
	];
	$string = str_replace(array_keys($puncTable), array_values($puncTable), $string);

	// Clean up multiple '-' characters
	$string = preg_replace('/-{2,}/', '-', $string);

	// Remove trailing '-' character if string not just '-'
	if ($string != '-') {
		$string = rtrim($string, '-');
	}

	if ($separator != '-') {
		$string = str_replace('-', $separator, $string);
	}

	return $string;
}

/**
 * @return mixed|string
 */
function detectLocale()
{
	$lang = detectLanguage();

	return (!$lang->isEmpty()) ? $lang->get('locale') : 'en_US';
}

/**
 * @return \Illuminate\Support\Collection
 */
function detectLanguage(): \Illuminate\Support\Collection
{
	$obj = new App\Helpers\Localization\Language();

	return $obj->find();
}

/**
 * Get file/folder permissions.
 *
 * @param string $path
 * @return string
 */
function getPerms(string $path): string
{
	return substr(sprintf('%o', fileperms($path)), -4);
}

/**
 * Get all countries from PHP array (umpirsky)
 *
 * @return array|null
 */
function getCountriesFromArray(): ?array
{
	$countries = new App\Helpers\Localization\Helpers\Country();
	$countries = $countries->all();

	if (empty($countries))
		return null;

	$arr = [];
	foreach ($countries as $code => $value) {
		if (!file_exists(storage_path('database/geonames/countries/' . strtolower($code) . '.sql'))) {
			continue;
		}
		$row = ['value' => $code, 'text' => $value];
		$arr[] = $row;
	}

	return $arr;
}

/**
 * Get all countries from DB (Geonames) & Translate them
 *
 * @param bool $includeNonActive
 * @return array
 */
function getCountries(bool $includeNonActive = false): array
{
	$arr = [];

	// Get installed countries list
	$countries = \App\Helpers\Localization\Country::getCountries($includeNonActive);

	if ($countries->count() > 0) {
		foreach ($countries as $code => $country) {
			// The country entry must be a Laravel Collection object
			if (!$country instanceof \Illuminate\Support\Collection) {
				$country = collect($country);
			}

			// Get the country data
			$code = ($country->has('code')) ? $country->get('code') : $code;
			$name = ($country->has('name')) ? $country->get('name') : '';
			$arr[$code] = $name;
		}
	}

	return $arr;
}

/**
 * Pluralization
 *
 * @param $number
 * @return int
 */
function getPlural($number)
{
	if (!is_numeric($number)) {
		$number = (int) $number;
	}

	if (config('lang.russian_pluralization')) {
		// Russian pluralization rules
		$typeOfPlural = (($number % 10 == 1) && ($number % 100 != 11))
			? 0
			: ((($number % 10 >= 2)
				&& ($number % 10 <= 4)
				&& (($number % 100 < 10)
					|| ($number % 100 >= 20)))
				? 1
				: 2
			);
	} else {
		// No rule for other languages
		$typeOfPlural = $number;
	}

	return $typeOfPlural;
}

/**
 * Get URL of Page by page's type
 *
 * @param string|null $type
 * @param string|null $locale
 * @return string
 * @throws \Exception
 */
function getUrlPageByType(?string $type, string $locale = null): string
{
	if (is_null($locale)) {
		$locale = config('app.locale');
	}

	$cacheExpiration = (int) config('settings.optimization.cache_expiration', 86400);
	$cacheId = 'page.' . $locale . '.type.' . $type;
	$page = cache()->remember($cacheId, $cacheExpiration, function () use ($type, $locale) {
		$page = \App\Models\Page::type($type)->first();

		if (!empty($page)) {
			$page->setLocale($locale);
		}

		return $page;
	});

	$linkTarget = '';
	$linkRel = '';
	if (!empty($page)) {
		if ($page->target_blank == 1) {
			$linkTarget = ' target="_blank"';
		}
		if (!empty($page->external_link)) {
			$linkRel = ' rel="nofollow"';
			$url = $page->external_link;
		} else {
			$url = \App\Helpers\UrlGen::page($page);
		}
	} else {
		$url = '#';
	}

	// Get attributes
	return 'href="' . $url . '"' . $linkRel . $linkTarget;
}

/**
 * @param string|null $uploadType
 * @param bool $jsFormat
 * @return array|false|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed|string|string[]
 */
function getUploadFileTypes(?string $uploadType = 'file', bool $jsFormat = false)
{
	if ($uploadType == 'image') {
		$types = config('settings.upload.image_types', 'jpg,jpeg,gif,png');
	} else {
		$types = config('settings.upload.file_types', 'pdf,doc,docx,word,rtf,rtx,ppt,pptx,odt,odp,wps,jpeg,jpg,bmp,png');
	}

	$separators = ['|', '-', ';', '.', '/', '_', ' '];
	$types = str_replace($separators, ',', $types);

	if ($jsFormat) {
		$types = explode(',', $types);
		$types = array_filter($types, function ($value) {
			return $value !== '';
		});
		$types = json_encode($types);
	}

	return $types;
}

/**
 * @param string|null $uploadType
 * @return array|mixed|string
 */
function showValidFileTypes(?string $uploadType = 'file')
{
	$formats = getUploadFileTypes($uploadType);

	return str_replace(',', ', ', $formats);
}

/**
 * Json To Array
 * NOTE: Used for MySQL Json and Laravel array (casts) columns
 *
 * @param array|object|string $string
 * @return array|mixed
 */
function jsonToArray($string)
{
	if (is_array($string)) {
		return $string;
	}

	if (is_object($string)) {
		return Arr::fromObject($string);
	}

	if (isJson($string)) {
		$array = json_decode($string, true);
		// If the JSON was encoded in JSON by mistake
		if (!is_array($array)) {
			return jsonToArray($array);
		}
	} else {
		$array = [];
	}

	return $array;
}

/**
 * Make sure that setting array contains only string, numeric or null elements
 *
 * @param $value
 * @return array|null
 */
function settingArrayElements($value): ?array
{
	if (!is_array($value)) {
		return null;
	}

	if (!empty($value)) {
		$array = [];
		foreach ($value as $subColumn => $subValue) {
			$array[$subColumn] = (is_string($subValue) || is_numeric($subValue)) ? $subValue : null;
		}
		$value = $array;
	}

	return $value;
}

/**
 * Check if variable contains (valid) JSON data
 *
 * @param $string
 * @return bool
 */
function isJson($string): bool
{
	return (is_string($string) && str($string)->isJson());
}

/**
 * Check if the string is a (valid) date
 *
 * @param string|null $value
 * @return bool
 */
function isValidDate(?string $value): bool
{
	$isValid = false;

	if (strtotime($value) !== false) {
		$value = date('Y-m-d H:i', strtotime($value));
	}

	$value = str_replace('/', '-', $value);
	$value = str_replace('.', '-', $value);

	if (\DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false) {
		$isValid = true;
	} else {
		if (\DateTime::createFromFormat('Y-m-d H:i', $value) !== false) {
			$isValid = true;
		} else {
			if (\DateTime::createFromFormat('Y-m-d', $value) !== false) {
				$isValid = true;
			}
		}
	}

	return $isValid;
}

/**
 * Check if exec() function is available
 *
 * @return boolean
 */
function phpExecFuncEnabled(): bool
{
	try {
		// make a small test
		exec("ls");

		return function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
	} catch (\Throwable $e) {
		return false;
	}
}

/**
 * Check if function is enabled
 *
 * @param string $name
 * @return bool
 */
function phpFuncEnabled(string $name): bool
{
	try {
		$disabled = array_map('trim', explode(',', ini_get('disable_functions')));

		return !in_array($name, $disabled);
	} catch (\Throwable $e) {
		return false;
	}
}

/**
 * Run artisan config cache
 *
 * @return mixed
 */
function artisanConfigCache()
{
	// Artisan config:cache generate the following two files
	// Since config:cache runs in the background
	// to determine if it is done, we just check if the files modified time have been changed
	$files = ['bootstrap/cache/config.php', 'bootstrap/cache/services.php'];

	// get the last modified time of the files
	$last = 0;
	foreach ($files as $file) {
		$path = base_path($file);
		if (file_exists($path)) {
			if (filemtime($path) > $last) {
				$last = filemtime($path);
			}
		}
	}

	// Prepare to run (5 seconds for $timeout)
	$timeout = 5;
	$start = time();

	// Actually call the Artisan command
	$exitCode = \Artisan::call('config:cache');

	// Check if Artisan call is done
	while (true) {
		// Just finish if timeout
		if (time() - $start >= $timeout) {
			echo "Timeout\n";
			break;
		}

		// If any file is still missing, keep waiting
		// If any file is not updated, keep waiting
		// @todo: services.php file keeps unchanged after artisan config:cache
		foreach ($files as $file) {
			$path = base_path($file);
			if (!file_exists($path)) {
				sleep(1);
				continue;
			} else {
				if (filemtime($path) == $last) {
					sleep(1);
					continue;
				}
			}
		}

		// Just wait another extra 3 seconds before finishing
		sleep(3);
		break;
	}

	return $exitCode;
}

/**
 * Run artisan migrate
 *
 * @return mixed
 */
function artisanMigrate()
{
	return \Artisan::call('migrate', ["--force" => true]);
}

/**
 * Check if the PHP Exif component is enabled
 *
 * @return bool
 */
function exifExtIsEnabled(): bool
{
	try {
		if (extension_loaded('exif') && function_exists('exif_read_data')) {
			return true;
		}

		return false;
	} catch (\Throwable $e) {
		return false;
	}
}

/**
 * @param string|null $purchaseCode
 * @param string|null $itemId
 * @return string
 */
function getPurchaseCodeApiEndpoint(?string $purchaseCode, string $itemId = null): string
{
	return config('larapen.core.purchaseCodeCheckerUrl') . $purchaseCode . '&domain=' . getDomain() . '&item_id=' . $itemId;
}

/**
 * Get Public File's URL
 *
 * @param string|null $filePath
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function fileUrl(?string $filePath)
{
	// Storage Disk Init.
	$disk = \App\Helpers\Files\Storage\StorageDisk::getDisk();

	try {
		return $disk->url($filePath);
	} catch (\Throwable $e) {
		return url('common/file?path=' . $filePath);
	}
}

/**
 * Get Private File's URL
 *
 * @param string|null $filePath
 * @param string|null $diskName
 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function privateFileUrl(?string $filePath, ?string $diskName = 'private')
{
	$queryString = 'path=' . $filePath;

	// For JC
	if (str_starts_with($filePath, 'resumes/')) {
		$diskName = 'private';
	}

	if (!empty($diskName)) {
		$queryString = 'disk=' . $diskName . '&' . $queryString;
	}

	return url('common/file?' . $queryString);
}

/**
 * Build HTML attributes with PHP array
 *
 * @param array|null $attributes
 * @return string
 */
function buildAttributes(?array $attributes): string
{
	if (empty($attributes)) {
		return '';
	}

	$attributePairs = [];
	foreach ($attributes as $key => $val) {
		if (is_int($key)) {
			$attributePairs[] = $val;
		} else {
			$val = htmlspecialchars($val, ENT_QUOTES);
			$attributePairs[] = "{$key}=\"{$val}\"";
		}
	}

	$out = trim(implode(' ', $attributePairs));

	if (!empty($out)) {
		$out = ' ' . $out;
	}

	return $out;
}

/**
 * @param string|null $filePath
 * @param string|null $preConfigSize
 * @param array|null $attr
 * @return string
 */
function imgTag(?string $filePath, ?string $preConfigSize = 'big', ?array $attr = []): string
{
	$src = imgUrl($filePath, $preConfigSize);
	$attr = buildAttributes($attr);

	$out = '';
	if (config('settings.optimization.webp_format')) {
		$srcWebp = imgUrl($filePath, $preConfigSize, true);

		if (!str_ends_with($srcWebp, '.webp')) {
			$out .= '<img src="' . $src . '"' . $attr . '>';
		} else {
			$out .= '<picture>';
			$out .= '<source srcset="' . $srcWebp . '" type="image/webp">';
			$out .= '<img src="' . $src . '"' . $attr . '>';
			$out .= '</picture>';
		}
	} else {
		$out .= '<img src="' . $src . '"' . $attr . '>';
	}

	return $out;
}

/**
 * @param string|null $filePath
 * @param string|null $preConfigSize
 * @param bool $webpFormat
 * @return string
 */
function imgUrl(?string $filePath, ?string $preConfigSize = 'big', bool $webpFormat = false): string
{
	// Storage Disk Init.
	$disk = \App\Helpers\Files\Storage\StorageDisk::getDisk();

	// Check if this is the default picture
	if (
		str_contains($filePath, config('larapen.core.logo'))
		|| str_contains($filePath, config('larapen.core.favicon'))
		|| str_contains($filePath, config('larapen.core.picture.default'))
		|| str_contains($filePath, config('larapen.core.avatar.default'))
		|| str_contains($filePath, config('larapen.admin.logo.dark'))
		|| str_contains($filePath, config('larapen.admin.logo.light'))
	) {
		return $disk->url($filePath) . getPictureVersion();
	}

	// Get pre-resized picture URL
	$picTypesAdmin = ['logo', 'cat', 'small', 'medium', 'big'];
	$picTypesOther = array_keys((array) config('larapen.core.picture.otherTypes'));
	$picTypesGlobal = array_merge($picTypesAdmin, $picTypesOther);
	if (!in_array($preConfigSize, $picTypesGlobal)) {
		try {
			return $disk->url($filePath) . getPictureVersion();
		} catch (\Throwable $e) {
			return url('common/file?path=' . $filePath) . getPictureVersion(true);
		}
	}

	// Check, Create thumbnail and Get its URL
	if ($webpFormat) {
		return resizeWebp($disk, $filePath, $preConfigSize);
	} else {
		return resize($disk, $filePath, $preConfigSize);
	}
}

/**
 * @param $disk
 * @param string|null $filePath
 * @param string|null $preConfigSize
 * @param bool $webpFormat
 * @return string
 */
function resize($disk, ?string $filePath, ?string $preConfigSize = 'big', bool $webpFormat = false): string
{
	// Image Quality
	$imageQuality = config('settings.upload.image_quality', 90);

	// Get Dimensions
	$defaultWidth = config('larapen.core.picture.otherTypes.' . $preConfigSize . '.width', 816);
	$defaultHeight = config('larapen.core.picture.otherTypes.' . $preConfigSize . '.height', 460);
	$width = (int) config('settings.upload.img_resize_' . $preConfigSize . '_width', $defaultWidth);
	$height = (int) config('settings.upload.img_resize_' . $preConfigSize . '_height', $defaultHeight);

	$filename = (!str_ends_with($filePath, DIRECTORY_SEPARATOR)) ? basename($filePath) : '';
	$fileDir = str_replace($filename, '', $filePath);
	$fileDir = rtrim($fileDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

	// WebP
	$filenameWithoutExtension = substr($filename, 0, strrpos($filename, '.'));
	$webpFilename = $filenameWithoutExtension . '.webp';
	if ($webpFormat) {
		$filename = $webpFilename;
	}

	// Thumb file name
	$sizeLabel = $width . 'x' . $height;
	$thumbFilename = 'thumb-' . $sizeLabel . '-' . $filename;
	$thumbFilePath = $fileDir . $thumbFilename;

	// Check if thumb image exists
	if (!$disk->exists($thumbFilePath)) {
		// Create thumb image if it not exists
		try {
			// Get file extension
			if ($webpFormat) {
				$extension = 'webp';
			} else {
				$extension = (is_png($disk->get($filePath))) ? 'png' : 'jpg';
			}

			// Init. Intervention
			$image = \Intervention\Image\Facades\Image::make($disk->get($filePath));

			// Get the image original dimensions
			$imgWidth = $image->width();
			$imgHeight = $image->height();

			// Manage Image By Type

			// Get Other Types Parameters
			if (in_array($preConfigSize, array_keys((array) config('larapen.core.picture.otherTypes')))) {
				// Get image manipulation settings
				$width = (int) config('larapen.core.picture.otherTypes.' . $preConfigSize . '.width', 900);
				$height = (int) config('larapen.core.picture.otherTypes.' . $preConfigSize . '.height', 900);
				$ratio = config('larapen.core.picture.otherTypes.' . $preConfigSize . '.ratio', '1');
				$upSize = config('larapen.core.picture.otherTypes.' . $preConfigSize . '.upsize', '0');

				// If the original dimensions are higher than the resize dimensions
				// OR the 'upsize' option is enable, then resize the image
				if ($imgWidth > $width || $imgHeight > $height) {
					// Resize
					$image = $image->resize($width, $height, function ($constraint) use ($ratio, $upSize) {
						if ($ratio == '1') {
							$constraint->aspectRatio();
						}
						if ($upSize == '1') {
							$constraint->upsize();
						}
					});
				}
			} else if (in_array($preConfigSize, ['logo', 'cat'])) {
				// Get image manipulation settings
				$ratio = config('settings.upload.img_resize_' . $preConfigSize . '_ratio', '1');
				$upSize = config('settings.upload.img_resize_' . $preConfigSize . '_upsize', '0');

				// If the original dimensions are higher than the resize dimensions
				// OR the 'upsize' option is enable, then resize the image
				if ($imgWidth > $width || $imgHeight > $height || $upSize == '1') {
					// Resize
					$image = $image->resize($width, $height, function ($constraint) use ($ratio, $upSize) {
						if ($ratio == '1') {
							$constraint->aspectRatio();
						}
						if ($upSize == '1') {
							$constraint->upsize();
						}
					});
				}
			} else if (in_array($preConfigSize, ['large', 'big', 'medium', 'small'])) {
				// Get image manipulation settings
				$resizeType = config('settings.upload.img_resize_' . $preConfigSize . '_resize_type', '0');
				$ratio = config('settings.upload.img_resize_' . $preConfigSize . '_ratio', '1');
				$upSize = config('settings.upload.img_resize_' . $preConfigSize . '_upsize', '0');
				$position = config('settings.upload.img_resize_' . $preConfigSize . '_position', 'center');
				$relative = config('settings.upload.img_resize_' . $preConfigSize . '_relative', false);
				$bgColor = config('settings.upload.img_resize_' . $preConfigSize . '_bg_color', 'ffffff');

				if ($resizeType == '0') {
					if ($imgWidth > $width || $imgHeight > $height || $upSize == '1') {
						// Resize
						$image = $image->resize($width, $height, function ($constraint) use ($ratio, $upSize) {
							if ($ratio == '1') {
								$constraint->aspectRatio();
							}
							if ($upSize == '1') {
								$constraint->upsize();
							}
						});
					}
				} else if ($resizeType == '1') {
					// Fit
					$image = $image->fit($width, $height, function ($constraint) use ($ratio, $upSize) {
						if ($ratio == '1') {
							$constraint->aspectRatio();
						}
						if ($upSize == '1') {
							$constraint->upsize();
						}
					});
				} else if ($resizeType == '2') {
					if ($imgWidth > $width || $imgHeight > $height || $upSize == '1') {
						// Resize (for ResizeCanvas)
						$image = $image->resize($width, $height, function ($constraint) use ($ratio, $upSize) {
							if ($ratio == '1') {
								$constraint->aspectRatio();
							}
							if ($upSize == '1') {
								$constraint->upsize();
							}
						});
					}
					// ResizeCanvas
					$image = $image->resizeCanvas($width, $height, $position, $relative, $bgColor)->resize($width, $height);
				} else {
					if ($imgWidth > $width || $imgHeight > $height) {
						// Resize (with hard parameters)
						$image = $image->resize($width, $height, function ($constraint) {
							$constraint->aspectRatio();
						});
					}
				}
			} else {
				if ($imgWidth > $width || $imgHeight > $height) {
					// Resize (with hard parameters)
					$image = $image->resize($width, $height, function ($constraint) {
						$constraint->aspectRatio();
					});
				}
			}

			// Encode the Image!
			$image = $image->encode($extension, $imageQuality);

		} catch (\Throwable $e) {
			$storageDisk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'));

			return $storageDisk->url($filePath) . getPictureVersion();
		}

		// Store the image on disk.
		$disk->put($thumbFilePath, $image->stream()->__toString());

		// Now delete temporary intervention image as we have moved it to Storage folder with Laravel filesystem.
		$image->destroy();
	}

	// Get the image URL
	try {
		return $disk->url($thumbFilePath) . getPictureVersion();
	} catch (\Throwable $e) {
		return url('common/file?path=' . $thumbFilePath) . getPictureVersion();
	}
}

/**
 * @param $disk
 * @param string|null $filePath
 * @param string|null $type
 * @return string
 */
function resizeWebp($disk, ?string $filePath, ?string $type = 'big'): string
{
	return resize($disk, $filePath, $type, true);
}

/**
 * Get pictures version
 *
 * @param bool $queryStringExists
 * @return string
 */
function getPictureVersion(bool $queryStringExists = false): string
{
	$pictureVersion = '';
	if (config('larapen.core.picture.versioned') && !empty(config('larapen.core.picture.version'))) {
		$pictureVersion .= ($queryStringExists) ? '&' : '?';
		$pictureVersion .= 'v=' . config('larapen.core.picture.version');
	}

	return $pictureVersion;
}

/**
 * @return string
 */
function vTime(): string
{
	$timeStamp = '?v=' . time();
	if (app()->environment(['staging', 'production'])) {
		$timeStamp = '';
	}

	return $timeStamp;
}

/**
 * Get image extension from base64 string
 *
 * @param string|null $bufferImg
 * @param bool $recursive
 * @return bool
 */
function is_png(?string $bufferImg, bool $recursive = true): bool
{
	$f = finfo_open();
	$result = finfo_buffer($f, $bufferImg, FILEINFO_MIME_TYPE);

	if (!str_contains($result, 'image') && $recursive) {
		// Plain Text
		return str_contains($bufferImg, 'image/png');
	}

	return $result == 'image/png';
}

/**
 * List of auth fields | List of notification channels
 *
 * @param bool $asChannel
 * @return array
 */
function getAuthFields(bool $asChannel = false): array
{
	$authFields = [
		'email' => $asChannel ? trans('settings.mail') : trans('global.email_address'),
	];

	$phoneIsEnabledAsAuthField = (config('settings.sms.enable_phone_as_auth_field') == '1');
	if ($phoneIsEnabledAsAuthField) {
		$authFields['phone'] = $asChannel ? trans('settings.sms') : trans('global.phone_number');
	}

	return $authFields;
}

/**
 * Get the auth field
 *
 * @param $entity
 * @return string
 */
function getAuthField($entity = null): string
{
	$authFields = array_keys(getAuthFields());
	$defaultAuthField = config('settings.sms.default_auth_field', 'email');

	// From default value
	$authField = $defaultAuthField;

	// From authenticated user's data
	$guard = isFromApi() ? 'sanctum' : null;
	if (auth($guard)->check()) {
		$savedValue = auth($guard)->user()->auth_field ?? $authField;
		$authField = (!empty($savedValue)) ? $savedValue : $authField;
	}

	// From a database table
	// '$entity' can be any table object that has 'auth_field' column
	if (!empty($entity)) {
		$savedValue = (is_array($entity))
			? ($entity['auth_field'] ?? $defaultAuthField)
			: ($entity->auth_field ?? $defaultAuthField);
		$authField = (!empty($savedValue)) ? $savedValue : $defaultAuthField;
	}

	// From form
	if (request()->filled('auth_field')) {
		$authField = request()->input('auth_field');
	}

	$authField = (in_array($authField, $authFields)) ? $authField : $defaultAuthField;

	$phoneIsEnabledAsAuthField = (config('settings.sms.enable_phone_as_auth_field') == '1');

	return ($phoneIsEnabledAsAuthField) ? $authField : 'email';
}

/**
 * Get the auth field name from its value
 *
 * @param string|null $value
 * @return string
 */
function getAuthFieldFromItsValue(?string $value = null): string
{
	$field = 'username';
	if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
		$field = 'email';
	} else if (preg_match('/^((\+|00)\d{1,3})?[\s\d]+$/', $value)) {
		$field = 'phone';
	}

	return $field;
}

/**
 * Get the auth field from the Token page
 *
 * @return string|null
 */
function getAuthFieldOnTokenPage(): ?string
{
	$authFields = array_keys(getAuthFields());

	// Get the right auth field
	$authField = null;
	if (request()->segment(2) == 'verify') {
		if (
			!empty(request()->segment(3))
			&& in_array(request()->segment(3), $authFields)
		) {
			$authField = request()->segment(3);
		}
	}

	return $authField;
}

/**
 * Get Phone's National Format
 *
 * Example: BE: 012/34.56.78 => 012 34 56 78
 *
 * @param string|null $phone
 * @return string
 */
function phoneNormalized(?string $phone): string
{
	$phone = ($phone !== null) ? $phone : '';

	return '' . preg_replace('/\D+/', '', $phone);
}

/**
 * Check if a phone number is valid (for a given country)
 *
 * @param string|null $phone
 * @param string|null $countryCode
 * @param string|null $type
 * @return bool
 */
function isValidPhoneNumber(?string $phone, ?string $countryCode = null, ?string $type = null): bool
{
	if (empty($phone) || empty($countryCode)) {
		return false;
	}

	$phone = phoneNormalized($phone);

	try {
		$validator = phone($phone, $countryCode);
		$isValid = $validator->isOfCountry($countryCode);
		if (!empty($type)) {
			$isValid = $validator->isOfType($type);
		}
	} catch (\Throwable $e) {
		$isValid = false;
	}

	return $isValid;
}

/**
 * Get Phone's National Format
 *
 * Example: BE: 012/34.56.78 => 012 34 56 78
 *
 * @param string|null $phone
 * @param string|null $countryCode
 * @return string|null
 */
function phoneNational(?string $phone, ?string $countryCode = null): ?string
{
	$phone = phoneNormalized($phone);

	try {
		$phone = phone($phone, $countryCode)->formatNational();
	} catch (\Throwable $e) {
		// Keep the default value
	}

	return $phone;
}

/**
 * Get Phone's E164 Format
 *
 * https://en.wikipedia.org/wiki/E.164
 * https://www.twilio.com/docs/glossary/what-e164
 *
 * Example: BE: 012 34 56 78 => +3212345678
 *
 * @param string|null $phone
 * @param string|null $countryCode
 * @return string|null
 */
function phoneE164(?string $phone, ?string $countryCode = null): ?string
{
	$phone = phoneNormalized($phone);

	try {
		$phone = phone($phone, $countryCode)->formatE164();
	} catch (\Throwable $e) {
		// Keep the default value
	}

	return $phone;
}

/**
 * Get Phone's International Format
 * Don't need to be saved in database
 *
 * Example: BE: 012 34 56 78 => +32 12 34 56 78
 *
 * @param string|null $phone
 * @param string|null $countryCode
 * @return string|null
 */
function phoneIntl(?string $phone, ?string $countryCode = null): ?string
{
	$phone = phoneNormalized($phone);

	try {
		$phone = phone($phone, $countryCode)->formatInternational();
	} catch (\Throwable $e) {
		// Keep the default value
	}

	return $phone;
}

/**
 * @param string|null $phone
 * @param string|null $provider
 * @return string|null
 */
function setPhoneSign(?string $phone, ?string $provider = null): ?string
{
	if ($provider == 'vonage') {
		// Vonage doesn't support the sign '+'
		if (str_starts_with($phone, '+')) {
			$phone = '' . str_replace('+', '', $phone);
		}
	}

	if ($provider == 'twilio') {
		// Twilio requires the sign '+'
		if (!str_starts_with($phone, '+')) {
			$phone = '+' . $phone;
		}
	}

	if (!in_array($provider, ['vonage', 'twilio'])) {
		if (!str_starts_with($phone, '+')) {
			$phone = '+' . $phone;
		}
	}

	return ($phone == '+') ? '' : $phone;
}

/**
 * @param $defaultCountryCode
 * @return mixed
 */
function getPhoneCountry($defaultCountryCode = null)
{
	$countryCode = $defaultCountryCode ?? (isFromApi() ? config('country.code') : session('countryCode'));
	$countryCode = request()->input('country_code', $countryCode);

	return request()->input('phone_country', $countryCode);
}

/**
 * @param bool $allowUserToChoose
 * @return bool
 */
function isUsersCanChooseNotifyChannel(bool $allowUserToChoose = false): bool
{
	$usersCanChooseNotifyChannel = (config('settings.sms.enable_phone_as_auth_field') == '1');
	if ($allowUserToChoose) {
		return $usersCanChooseNotifyChannel;
	}

	if (auth()->check()) {
		$usersCanChooseNotifyChannel = (
			$usersCanChooseNotifyChannel
			&& config('settings.sms.messenger_notifications') == '1'
		);
	}

	return $usersCanChooseNotifyChannel;
}

/**
 * @return bool
 */
function isBothAuthFieldsCanBeDisplayed(): bool
{
	$emailNeedToBeVerified = (config('settings.mail.email_verification') == '1');
	$phoneNeedToBeVerified = (config('settings.sms.phone_verification') == '1');

	$isBothAuthFieldNeedToBeVerified = ($emailNeedToBeVerified && $phoneNeedToBeVerified);
	$isBothAuthFieldsCanBeDisplayed = (bool) config('larapen.core.displayBothAuthFields');

	if ($isBothAuthFieldNeedToBeVerified) {
		return false;
	}

	return $isBothAuthFieldsCanBeDisplayed;
}

/**
 * @return array|\Illuminate\Contracts\Translation\Translator|string|null
 */
function getTokenLabel()
{
	$authField = getAuthFieldOnTokenPage();

	if ($authField == 'email') {
		return t('Code received by Email');
	}
	if ($authField == 'phone') {
		return t('Code received by SMS');
	}

	return t('Code received by SMS or Email');
}

/**
 * @return array|\Illuminate\Contracts\Translation\Translator|string|null
 */
function getTokenMessage()
{
	$authField = getAuthFieldOnTokenPage();

	if ($authField == 'email') {
		return t('Enter the code you received by Email in the field below');
	}
	if ($authField == 'phone') {
		return t('Enter the code you received by SMS in the field below');
	}

	return t('Enter the code you received by SMS or Email in the field below');
}

/**
 * Replace global variables patterns from string
 *
 * @param string|null $string
 * @param bool $removeUnmatchedPatterns
 * @return string|string[]
 */
function replaceGlobalPatterns(?string $string, bool $removeUnmatchedPatterns = true)
{
	$string = str_replace('{app.name}', config('app.name'), $string);
	$string = str_replace('{country.name}', config('country.name'), $string);
	$string = str_replace('{country}', config('country.name'), $string);

	if (config('settings.app.slogan')) {
		$string = str_replace('{app.slogan}', config('settings.app.slogan'), $string);
	}

	if (str_contains($string, '{count.listings}')) {
		try {
			$countPosts = \App\Models\Post::whereHas('country')->currentCountry()->unarchived()->count();
		} catch (\Throwable $e) {
			$countPosts = 0;
		}
		$string = str_replace('{count.listings}', $countPosts, $string);
	}
	if (str_contains($string, '{count.users}')) {
		try {
			$countUsers = \App\Models\User::count();
		} catch (\Throwable $e) {
			$countUsers = 0;
		}
		$string = str_replace('{count.users}', $countUsers, $string);
	}

	if ($removeUnmatchedPatterns) {
		$string = removeUnmatchedPatterns($string);
	}

	return $string;
}

/**
 * Remove all unmatched variables patterns (e.g. {foo}) from a string
 *
 * @param string|null $string
 * @return string
 */
function removeUnmatchedPatterns(?string $string): string
{
	$string = preg_replace('|\{[^\}]+\}|ui', '', $string);
	$string = preg_replace('|,([\s]*,)+|ui', ',', $string);
	$string = preg_replace('|\s\s+|ui', ' ', $string);

	return trim($string, " \n\r\t\v\0,-");
}

/**
 * Get meta tag from settings
 *
 * @param string|null $page
 * @return array
 */
function getMetaTag(?string $page): array
{
	$metaTag = ['title' => '', 'description' => '', 'keywords' => ''];

	// Check if the Domain Mapping plugin is available
	if (config('plugins.domainmapping.installed')) {
		$metaTag = \extras\plugins\domainmapping\Domainmapping::getMetaTag($page);
		if (!empty($metaTag) && !arrayItemsAreEmpty($metaTag)) {
			return $metaTag;
		}
	}

	// Get the current Language
	$languageCode = config('lang.abbr', config('app.locale'));

	// Get the Page's MetaTag
	$model = null;
	try {
		$cacheExpiration = (int) config('settings.optimization.cache_expiration', 86400);
		$cacheId = 'metaTag.' . $languageCode . '.' . $page;
		$model = cache()->remember($cacheId, $cacheExpiration, function () use ($languageCode, $page) {
			$model = \App\Models\MetaTag::where('page', $page)->first(['title', 'description', 'keywords']);

			if (!empty($model)) {
				$model->setLocale($languageCode);
				$model = $model->toArray();
			}

			return $model;
		});
	} catch (\Throwable $e) {
	}

	if (!empty($model)) {
		$metaTag = $model;

		$metaTag['title'] = getColumnTranslation($metaTag['title'], $languageCode);
		$metaTag['description'] = getColumnTranslation($metaTag['description'], $languageCode);
		$metaTag['keywords'] = getColumnTranslation($metaTag['keywords'], $languageCode);

		$metaTag['title'] = replaceGlobalPatterns($metaTag['title'], false);
		$metaTag['description'] = replaceGlobalPatterns($metaTag['description'], false);
		$metaTag['keywords'] = mb_strtolower(replaceGlobalPatterns($metaTag['keywords'], false));

		return array_values($metaTag);
	}

	$pagesThatHaveTheirOwnDefaultMetaTags = [
		'search',
		'searchCategory',
		'searchLocation',
		'searchProfile',
		'searchTag',
		'listingDetails',
		'staticPage',
	];

	if (!in_array($page, $pagesThatHaveTheirOwnDefaultMetaTags)) {
		if (config('settings.app.slogan')) {
			$metaTag['title'] = config('app.name') . ' - ' . config('settings.app.slogan');
		} else {
			$metaTag['title'] = config('app.name') . ' - ' . config('country.name');
		}
		$metaTag['description'] = $metaTag['title'];
	}

	if (!is_array($metaTag)) {
		$metaTag = [];
	}
	$metaTag['title'] = $metaTag['title'] ?? null;
	$metaTag['description'] = $metaTag['description'] ?? null;
	$metaTag['keywords'] = $metaTag['keywords'] ?? null;

	return is_array($metaTag) ? array_values($metaTag) : [];
}

/**
 * Check if an array contains only empty items/elements
 *
 * @param array|null $array
 * @return bool
 * @todo: Make it recursive
 */
function arrayItemsAreEmpty(?array $array): bool
{
	if (empty($array)) {
		return true;
	}

	// Check if the array contains a non-empty element
	$newArray = $array;
	foreach ($array as $key => $value) {
		if (empty($value) && array_key_exists($key, $newArray)) {
			unset($newArray[$key]);
		}
	}
	if (!empty($newArray)) {
		return false;
	} else {
		return true;
	}
}

/**
 * Redirect (Prevent Browser Cache)
 *
 * @param string $url
 * @param int $code (301 => Moved Permanently | 302 => Moved Temporarily)
 * @param array $headers
 */
function redirectUrl(string $url, int $code = 301, array $headers = [])
{
	// Headers have been sent
	// Any more header lines can't add using the header() function once the header block has already been sent.
	if (headers_sent()) {
		redirectUrlWithHtml($url);

		return;
	}

	if (is_array($headers) && !empty($headers)) {
		foreach ($headers as $key => $value) {
			if (str_contains($value, 'post-check') || str_contains($value, 'pre-check')) {
				header($key . ": " . $value, false);
			} else {
				header($key . ": " . $value);
			}
		}
	}

	header("Location: " . $url, true, $code);
	exit();
}

/**
 * Redirect URL (with GET method) in HTML
 * Note: Don't prevent browser cache
 *
 * @param string $url
 * @return void
 */
function redirectUrlWithHtml(string $url)
{
	$out = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redirection...</title>
        <script type="text/javascript">
            window.location.href = "' . $url . '"
        </script>
        <noscript>
        	<meta http-equiv="refresh" content="0; url=' . $url . '">
        </noscript>
    </head>
    <body>
        If you are not redirected automatically, follow this <a href="' . $url . '">link</a>.
    </body>
</html>';

	echo $out;
	exit();
}

/**
 * Split a name into the first name and last name
 *
 * @param string|null $input
 * @return array
 */
function splitName(?string $input): array
{
	$output = [];

	$space = mb_strpos($input, ' ');
	if ($space !== false) {
		$output['firstName'] = mb_substr($input, 0, $space);
		$output['lastName'] = mb_substr($input, $space, strlen($input));
	} else {
		$output['firstName'] = '';
		$output['lastName'] = $input;
	}

	return $output;
}

/**
 * Zero leading for numeric values
 *
 * @param string|null $value
 * @param int $padLength
 * @return string|null
 */
function zeroLead(?string $value, int $padLength = 2): ?string
{
	if (is_numeric($value)) {
		$value = str_pad($value, $padLength, '0', STR_PAD_LEFT);
	}

	return $value;
}

/**
 * Get the Distance Calculation Unit
 *
 * @param string|null $countryCode
 * @return string
 */
function getDistanceUnit(string $countryCode = null)
{
	if (empty($countryCode)) {
		$countryCode = config('country.code');
	}
	$unit = \Larapen\LaravelDistance\Helper::getDistanceUnit($countryCode);

	return t($unit);
}

/**
 * Check if the app's installation files exist
 *
 * @return bool
 */
function appInstallFilesExist(): bool
{
	// Check if the '.env' and 'storage/installed' files exist
	if (file_exists(base_path('.env')) && file_exists(storage_path('installed'))) {
		return true;
	}

	return false;
}

/**
 * Check if the app is installed
 *
 * @return bool
 */
function appIsInstalled(): bool
{
	// Check if the app's installation files exist
	return appInstallFilesExist();
}

/**
 * Check if the app is being installed or upgraded
 *
 * @return bool
 */
function appIsBeingInstalledOrUpgraded(): bool
{
	return (
		str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'InstallController')
		|| str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'UpgradeController')
	);
}

/**
 * Check if an update is available
 *
 * @return bool
 */
function updateIsAvailable(): bool
{
	return false;
	/*
	
	// Check if the '.env' file exists
	if (!file_exists(base_path('.env'))) {
		return false;
	}

	$updateIsAvailable = false;

	// Get eventual new version value & the current (installed) version value
	$lastVersion = getLatestVersion();
	$currentVersion = getCurrentVersion();

	// Check the update
	if (version_compare($lastVersion, $currentVersion, '>')) {
		$updateIsAvailable = true;
	}

	return $updateIsAvailable;
	
	*/
}

/**
 * Get the script possible URL base
 *
 * @return string
 */
function getRawBaseUrl(): string
{
	// Get the Laravel App public path name
	$laravelPublicPath = trim(public_path(), '/');
	$laravelPublicPathLabel = last(explode('/', $laravelPublicPath));

	// Get Server Variables
	$httpHost = (trim(request()->server('HTTP_HOST')) != '') ? request()->server('HTTP_HOST') : ($_SERVER['HTTP_HOST'] ?? '');
	$requestUri = (trim(request()->server('REQUEST_URI')) != '') ? request()->server('REQUEST_URI') : ($_SERVER['REQUEST_URI'] ?? '');

	// Clear the Server Variables
	$httpHost = trim($httpHost, '/');
	$requestUri = trim($requestUri, '/');
	$requestUri = (mb_substr($requestUri, 0, strlen($laravelPublicPathLabel)) === $laravelPublicPathLabel) ? '/' . $laravelPublicPathLabel : '';

	// Get the Current URL
	$currentUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://') . $httpHost . strtok($requestUri, '?');
	$currentUrl = head(explode('/' . admin_uri(), $currentUrl));

	// Get the Base URL
	$baseUrl = head(explode('/install', $currentUrl));

	return rtrim($baseUrl, '/');
}

/**
 * Get the current version value
 *
 * @return null|string
 */
function getCurrentVersion(): ?string
{
	// Get the Current Version
	$version = null;
	if (\Jackiedo\DotenvEditor\Facades\DotenvEditor::keyExists('APP_VERSION')) {
		try {
			$version = \Jackiedo\DotenvEditor\Facades\DotenvEditor::getValue('APP_VERSION');
		} catch (\Throwable $e) {
		}
	}

	return checkAndUseSemVer($version);
}

/**
 * Get the app's latest version
 *
 * @return string
 */
function getLatestVersion(): string
{
	return checkAndUseSemVer(config('app.appVersion'));
}

/**
 * Check and use semver version num format
 *
 * @param string|null $version
 * @return string
 */
function checkAndUseSemVer(?string $version): string
{
	$semver = '0.0.0';
	if (!empty($version)) {
		$numPattern = '([0-9]+)';
		if (preg_match('#^' . $numPattern . '\.' . $numPattern . '\.' . $numPattern . '$#', $version)) {
			$semver = $version;
		} else {
			if (preg_match('#^' . $numPattern . '\.' . $numPattern . '$#', $version)) {
				$semver = $version . '.0';
			} else {
				if (preg_match('#^' . $numPattern . '$#', $version)) {
					$semver = $version . '.0.0';
				} else {
					$semver = '0.0.0';
				}
			}
		}
	}

	return $semver;
}

/**
 * Extract only digit characters
 *
 * @param string|null $value
 * @param int|null $default
 * @return string|int|null
 */
function strToDigit(?string $value, int $default = null)
{
	$value = trim(preg_replace('/[^0-9]/', '', $value));
	if (empty($value)) {
		$value = $default;
	}

	return $value;
}

/**
 * Extract only digit characters and Convert the result in integer
 *
 * @param string|null $value
 * @param int $default
 * @return int
 */
function strToInt(?string $value, int $default = 0): int
{
	return (int) strToDigit($value, $default);
}

/**
 * Change whitespace (\n and \r) to simple space in string
 * PHP_EOL catches newlines that \n, \r\n, \r miss.
 *
 * @param string|null $string
 * @return array|string|string[]
 */
function changeWhiteSpace(?string $string)
{
	return str_replace(PHP_EOL, ' ', $string);
}

/**
 * PHP round() function that always return a float value in any language
 *
 * @param float|int $val
 * @param int $precision
 * @param int $mode
 * @return string
 */
function round_val($val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): string
{
	return number_format((float) round($val, $precision, $mode), $precision, '.', '');
}

/**
 * Print JavaScript code in HTML
 *
 * @param string|null $code
 * @return array|string|string[]
 */
function printJs(?string $code)
{
	// Get the External JS, and make for them a pattern
	$exRegex = '/<script([a-z0-9\-_ ]+)src=([^>]+)>(.*?)<\/script>/ius';
	$replace = '<#EXTERNALJS#$1src=$2>$3</#EXTERNALJS#>';
	$code = preg_replace($exRegex, $replace, $code);

	// Get the Inline JS, and make for them a pattern
	$inRegex = '/<script([^>]*)>(.*?)<\/script>/ius';
	$replace = '<#INLINEJS#$1>$2</#INLINEJS#>';
	while (preg_match($inRegex, $code)) {
		$code = preg_replace($inRegex, $replace, $code);
	}

	// Replace the patterns
	$code = str_replace(['#EXTERNALJS#', '#INLINEJS#'], 'script', $code);

	// The code doesn't contain a <script> tag
	if (!preg_match($inRegex, $code)) {
		$code = '<script type="text/javascript">' . "\n" . $code . "\n" . '</script>';
	}

	return $code;
}

/**
 * Print CSS codes in HTML
 *
 * @param string|null $code
 * @return string
 */
function printCss(?string $code): string
{
	$code = preg_replace('/<[^>]+>/i', '', $code);
	$code = '<style>' . "\n" . $code . "\n" . '</style>';

	return $code;
}

/**
 * Get Front Skin
 *
 * @param string|null $skin
 * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
 */
function getFrontSkin(string $skin = null)
{
	$savedSkin = config('settings.style.skin', 'default');

	if (!empty($skin)) {
		$skinsArray = config('larapen.core.skins');
		if (!is_array($skinsArray) || !array_key_exists($skin, $skinsArray)) {
			$skin = $savedSkin;
		}
	} else {
		$skin = $savedSkin;
	}

	return $skin;
}

/**
 * Count the total number of line of a given file without loading the entire file.
 * This is effective for large file
 *
 * @param string $path
 * @return int
 */
function lineCount(string $path): int
{
	$file = new \SplFileObject($path, 'r');
	$file->seek(PHP_INT_MAX);

	return $file->key() + 1;
}

/**
 * Escape characters with slashes like in C & Remove the double white spaces
 *
 * @param string|null $string
 * @param string $quote
 * @return null|string|string[]
 */
function addcslashesLite(?string $string, string $quote = '"')
{
	return preg_replace("/\s+/ui", " ", addcslashes($string, $quote));
}

/**
 * Get the current request path by pattern
 *
 * @param string|null $pattern
 * @return string
 */
function getRequestPath(string $pattern = null): string
{
	if (empty($pattern)) {
		return request()->path();
	}

	$pattern = '#(' . $pattern . ')#ui';

	$tmp = '';
	preg_match($pattern, request()->path(), $tmp);

	return (isset($tmp[1]) && !empty($tmp[1])) ? $tmp[1] : request()->path();
}

/**
 * Get random password
 *
 * @param int $length
 * @return string
 */
function getRandomPassword(int $length)
{
	$allowedCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!$%^&#!$%^&#';
	$random = str_shuffle($allowedCharacters);
	$password = substr($random, 0, $length);

	if (is_bool($password) || empty($password)) {
		$password = Str::random($length);
	}

	return $password;
}

/**
 * Get a unique code
 *
 * @param int $limit
 * @return string
 */
function uniqueCode(int $limit)
{
	$uniqueCode = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);

	if (is_bool($uniqueCode) || empty($uniqueCode)) {
		$uniqueCode = Str::random($limit);
	}

	return $uniqueCode;
}

/**
 * Hashids is a small PHP library to generate YouTube-like ids from numbers.
 * Use it when you don't want to expose your database numeric ids to users
 *
 * @param $in
 * @param bool $toNum
 * @param bool $withPrefix
 * @param int $minHashLength
 * @param string $salt
 * @return array|mixed|string|null
 */
function hashId($in, bool $toNum = false, bool $withPrefix = true, int $minHashLength = 11, string $salt = '')
{
	if (!config('settings.seo.listing_hashed_id_enabled') && !isHashedId($in)) {
		return $in;
	}

	$hidPrefix = $withPrefix ? config('larapen.core.hashableIdPrefix') : '';
	$hidPrefix = is_string($hidPrefix) ? $hidPrefix : '';

	$hashIds = new \Hashids\Hashids($salt, $minHashLength);

	if (!$toNum) {
		$out = $hidPrefix . $hashIds->encode($in);
	} else {
		$in = ltrim($in, $hidPrefix);
		$out = $hashIds->decode($in);
		if (isset($out[0])) {
			$out = $out[0];
		}
	}

	return !empty($out) ? $out : null;
}

/**
 * @param $in
 * @param int $minHashLength
 * @return bool
 */
function isHashedId($in, int $minHashLength = 11): bool
{
	$hidPrefix = config('larapen.core.hashableIdPrefix');
	$hidPrefixLength = is_string($hidPrefix) ? strlen($hidPrefix) : 0;

	return (
		preg_match('/[a-z0-9A-Z]+/', $in)
		&& (strlen($in) == ($minHashLength + $hidPrefixLength))
	);
}

/**
 * Get routes prefixes to ban to match listing route's path
 *
 * @return array
 */
function regexSimilarRoutesPrefixes(): array
{
	$routes = (array) config('routes');

	$prefixes = [];
	foreach ($routes as $route) {
		$prefix = head(explode('/', $route));
		if (!str_starts_with($prefix, '{')) {
			$prefixes[] = $prefix;
		}
	}

	return array_unique($prefixes);
}

if (!function_exists('ietfLangTag')) {
	/**
	 * IETF language tag(s)
	 * Example: en-US, pt-BR, fr-CA, ... (Usage of "-" instead of "_")
	 *
	 * @param string|null $locale
	 * @return array|string|string[]|null
	 */
	function ietfLangTag(string $locale = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}

		return str_replace('_', '-', $locale);
	}
}

/**
 * Check if the user browser is the given value.
 * The given value can be:
 * 'Firefox', 'Chrome', 'Safari', 'Opera', 'MSIE', 'Trident', 'Edge'
 *
 * Usage: doesUserBrowserIs('Chrome') or doesUserBrowserIs() == 'Chrome'
 *
 * @param string|null $browser
 * @return bool
 */
function doesUserBrowserIs(string $browser = null): bool
{
	if (!empty($browser)) {
		return (str_contains(request()->server('HTTP_USER_AGENT'), $browser));
	} else {
		$browsers = ['Firefox', 'Chrome', 'Safari', 'Opera', 'MSIE', 'Trident', 'Edge'];
		$agent = request()->server('HTTP_USER_AGENT');

		$userBrowser = null;
		foreach ($browsers as $browser) {
			if (str_contains($agent, $browser)) {
				$userBrowser = $browser;
				break;
			}
		}

		return !empty($userBrowser);
	}
}

/**
 * Get sitemaps indexes
 *
 * @param bool $htmlFormat
 * @return string
 */
function getSitemapsIndexes(bool $htmlFormat = false): string
{
	$out = '';

	$countries = \App\Helpers\Localization\Helpers\Country::transAll(\App\Helpers\Localization\Country::getCountries());
	if (!$countries->isEmpty()) {
		if ($htmlFormat) {
			$cmFieldStyle = ($countries->count() > 10) ? ' style="height: 205px; overflow-y: scroll;"' : '';
			$out .= '<ul' . $cmFieldStyle . '>';
		}
		foreach ($countries as $country) {
			$country = \App\Helpers\Localization\Country::getCountryInfo($country->get('code'));

			if ($country->isEmpty()) {
				continue;
			}

			// Get the Country's Language Code
			$countryLanguageCode = ($country->has('lang') && $country->get('lang')->has('abbr'))
				? $country->get('lang')->get('abbr')
				: config('app.locale');

			// Add the Sitemap Index
			if ($htmlFormat) {
				$out .= '<li>' . dmUrl($country, $country->get('icode') . '/sitemaps.xml') . '</li>';
			} else {
				$out .= 'Sitemap: ' . dmUrl($country, $country->get('icode') . '/sitemaps.xml') . "\n";
			}
		}
		if ($htmlFormat) {
			$out .= '</ul>';
		}
	}

	return $out;
}

/**
 * Default robots.txt content
 *
 * @return string
 */
function getDefaultRobotsTxtContent(): string
{
	$out = 'User-agent: *' . "\n";
	$out .= 'Disallow:' . "\n";
	$out .= "\n";
	$out .= 'Allow: /' . "\n";
	$out .= "\n";
	$out .= 'User-agent: *' . "\n";
	$out .= 'Disallow: /' . admin_uri() . '/' . "\n";
	$out .= 'Disallow: /ajax/' . "\n";
	$out .= 'Disallow: /assets/' . "\n";
	$out .= 'Disallow: /css/' . "\n";
	$out .= 'Disallow: /js/' . "\n";
	$out .= 'Disallow: /vendor/' . "\n";
	$out .= 'Disallow: /main.php' . "\n";
	$out .= 'Disallow: /index.php' . "\n";
	$out .= 'Disallow: /mix-manifest.json' . "\n";

	$languages = getSupportedLanguages();
	if (!empty($languages)) {
		foreach ($languages as $code => $lang) {
			$out .= 'Disallow: /locale/' . $code . "\n";
		}
	}

	$providers = ['facebook', 'linkedin', 'twitter', 'google'];
	foreach ($providers as $provider) {
		$out .= 'Disallow: /auth/' . $provider . "\n";
	}

	return $out;
}

/**
 * Generate the Email Form button
 *
 * @param null $post
 * @param bool $btnBlock
 * @param bool $iconOnly
 * @return string
 */
function genEmailContactBtn($post = null, bool $btnBlock = false, bool $iconOnly = false): string
{
	$post = (is_array($post)) ? Arr::toObject($post) : $post;

	$out = '';

	if (!isVerifiedPost($post)) {
		return $out;
	}

	$smsNotificationCanBeSent = (
		config('settings.sms.enable_phone_as_auth_field') == '1'
		&& config('settings.sms.messenger_notifications') == '1'
		&& $post->auth_field == 'phone'
		&& !empty($post->phone)
	);
	if (empty($post->email) && !$smsNotificationCanBeSent) {
		if ($iconOnly) {
			$out = '<i class="far fa-envelope" style="color: #dadada"></i>';
		}

		return $out;
	}

	$btnLink = '#contactUser';
	$btnClass = '';
	if (!auth()->check()) {
		if (config('settings.single.guests_can_contact_authors') != '1') {
			$btnLink = '#quickLogin';
		}
	}

	if ($iconOnly) {
		$out .= '<a href="' . $btnLink . '" data-bs-toggle="modal">';
		$out .= '<div class="w-6 fill-green-700" title="' . t('Send a message') . '">
					<svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
						<path d="M24 3.79l-9 10.05a4.22 4.22 0 0 1-3 1.64 4.36 4.36 0 0 1-3-1.7L0 3.79v13.68a8.94 8.94 0 0 0 .1 1.27l5.23-5.37 1.34 1.35-5.34 5.37a10.11 10.11 0 0 0 1.34.12H22a1.69 1.69 0 0 0 .53-.12l-5.2-5.37 1.34-1.35 5.15 5.37a5.52 5.52 0 0 0 .18-1.52V3.79zm-11.24 9.27l8.57-9.27H2.67l8.57 9.27a1.26 1.26 0 0 0 .76.44 1.26 1.26 0 0 0 .76-.44z" fill-rule="evenodd"></path>
					</svg>
				</div> ';
	} else {
		if ($btnBlock) {
			$btnClass = $btnClass . ' btn-block';
		}

		$out .= '<a href="' . $btnLink . '" data-bs-toggle="modal" class="ml-1 flex items-center' . $btnClass . '">';
		$out .= '<div class="w-6 fill-green-700">
					<svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
						<path d="M24 3.79l-9 10.05a4.22 4.22 0 0 1-3 1.64 4.36 4.36 0 0 1-3-1.7L0 3.79v13.68a8.94 8.94 0 0 0 .1 1.27l5.23-5.37 1.34 1.35-5.34 5.37a10.11 10.11 0 0 0 1.34.12H22a1.69 1.69 0 0 0 .53-.12l-5.2-5.37 1.34-1.35 5.15 5.37a5.52 5.52 0 0 0 .18-1.52V3.79zm-11.24 9.27l8.57-9.27H2.67l8.57 9.27a1.26 1.26 0 0 0 .76.44 1.26 1.26 0 0 0 .76-.44z" fill-rule="evenodd"></path>
					</svg>
				</div> ';
		$out .= '<span class="ml-4">' . t('Send a message') . '<span>';
	}
	$out .= '</a>';

	return $out;
}

/**
 * Generate the Phone Number button
 *
 * @param $post
 * @param bool $btnBlock
 * @return string
 */
function genPhoneNumberBtn($post, bool $btnBlock = false): string
{
	$post = (is_array($post)) ? Arr::toObject($post) : $post;

	$out = '';

	if (empty($post->phone_intl) || $post->phone_hidden == 1) {
		return $out;
	}

	$enableWhatsAppBtn = (config('settings.single.enable_whatsapp_btn') == 1);
	$whatsAppPreFilledMessage = (config('settings.single.pre_filled_whatsapp_message') == 1)
		? '?text=' . rawurlencode(t('whatsapp_pre_filled_message', [
			'title' => $post->title,
			'appName' => config('app.name'),
		])) : '';
	$whatsAppLink = 'https://wa.me/' . strToDigit($post->phone) . $whatsAppPreFilledMessage;
	$waBtnClass = '';

	$btnLink = 'tel:' . $post->phone;
	$btnAttr = '';
	$btnClass = ' phoneBlock'; /* for the JS showPhone() function */
	$btnHint = t('Click to see');
	$phone = $post->phone_intl;
	if (config('settings.single.hide_phone_number')) {
		$phoneToHide = phoneNormalized($phone);
		if (config('settings.single.hide_phone_number') == '1') {
			$phone = str($phoneToHide)->mask('X', -str($phoneToHide)->length(), str($phoneToHide)->length() - 3);
		}
		if (config('settings.single.hide_phone_number') == '2') {
			$phone = str($phoneToHide)->mask('X', 3);
		}
		if (config('settings.single.hide_phone_number') == '3') {
			$phone = str($phoneToHide)->mask('X', 0);
		}
		$btnLink = '';
		$btnAttrTooltip = 'data-bs-toggle="tooltip" data-bs-placement="bottom" title="' . $btnHint . '"';
		$btnClassTooltip = '';

		$btnAttr = $btnAttrTooltip;
		$btnClass = $btnClass . $btnClassTooltip;

		$enableWhatsAppBtn = false;
	} else {
		if (config('settings.single.convert_phone_number_to_img')) {
			try {
				$phone = \Larapen\TextToImage\Facades\TextToImage::make($phone, config('larapen.core.textToImage'));
			} catch (\Throwable $e) {
				$phone = $post->phone;
			}
			$btnClass = '';
		}
	}

	if (config('settings.single.show_security_tips') == '1') {
		/*
				  Set multiple data-bs-toggle for link in Bootstrap
				  Tooltip + modal in button - Bootstrap
				  
				  Usage of '[rel="tooltip"]' as selector instead of '[data-bs-toggle="tooltip"]' for the tooltip,
				  and trigger that with on hover event from JS
			  */
		$btnAttrTooltip = 'rel="tooltip" data-bs-placement="bottom" title="' . $btnHint . '"';
		$btnClassTooltip = '';
		$btnAttrModal = 'data-bs-toggle="modal"';

		$btnLink = '#securityTips';
		$btnAttr = $btnAttrModal . ' ' . $btnAttrTooltip;
		$btnClass = ' phoneBlock'; /* for the JS showPhone() function */
		if (!config('settings.single.hide_phone_number')) {
			$phone = t('phone_number');
		}
		$btnClass = $btnClass . ' ' . $btnClassTooltip;
	}

	if (!auth()->check()) {
		if (config('settings.single.guests_can_contact_authors') != '1') {
			$btnAttrModal = 'data-bs-toggle="modal"';

			$phone = $btnHint;
			$btnLink = '#quickLogin';
			$btnAttr = $btnAttrModal;
			$btnClass = '';

			$enableWhatsAppBtn = false;
		}
	}

	if ($btnBlock) {
		$waBtnClass = $waBtnClass . ' btn-block';
		$btnClass = $btnClass . ' btn-block';
	}

	// Generate the Phone Number button
	$out .= '<a href="' . $btnLink . '" ' . $btnAttr . ' class="btn btn-warning' . $btnClass . '">';
	$out .= '<i class="fas fa-mobile-alt"></i> ';
	$out .= $phone;
	$out .= '</a>';

	if ($enableWhatsAppBtn) {
		$waBtnAttr = 'data-bs-toggle="tooltip" data-bs-placement="bottom" title="' . t('chat_on_whatsapp') . '"';
		$waBtnClass = $waBtnClass . '';

		// Generate the WhatsApp button
		$out .= '<a href="' . $whatsAppLink . '" ' . $waBtnAttr . ' target="_blank" class="btn btn-success' . $waBtnClass . '">';
		$out .= '<i class="fab fa-whatsapp"></i> ';
		$out .= 'WhatsApp';
		$out .= '</a>';
	}

	return $out;
}

/**
 * Set the Backup config vars
 *
 * @param string|null $typeOfBackup
 */
function setBackupConfig(string $typeOfBackup = null)
{
	// Get the current version value
	$version = preg_replace('/[^\d+]/', '', config('app.appVersion'));

	// All backup filename prefix
	config()->set('backup.backup.destination.filename_prefix', 'site-v' . $version . '-');

	// Database backup
	if ($typeOfBackup == 'database') {
		config()->set('backup.backup.admin_flags', [
			'--disable-notifications' => true,
			'--only-db' => true,
		]);
		config()->set('backup.backup.destination.filename_prefix', 'database-v' . $version . '-');
	}

	// Languages' files backup
	if ($typeOfBackup == 'languages') {
		$include = [
			lang_path(),
		];
		$pluginsDirs = glob(config('larapen.core.plugin.path') . '*', GLOB_ONLYDIR);
		if (!empty($pluginsDirs)) {
			foreach ($pluginsDirs as $pluginDir) {
				$pluginLangFolder = $pluginDir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
				if (file_exists($pluginLangFolder)) {
					$include[] = $pluginLangFolder;
				}
			}
		}

		config()->set('backup.backup.admin_flags', [
			'--disable-notifications' => true,
			'--only-files' => true,
		]);
		config()->set('backup.backup.source.files.include', $include);
		config()->set('backup.backup.source.files.exclude', [
			//...
		]);
		config()->set('backup.backup.destination.filename_prefix', 'languages-');
	}

	// Generated files backup
	if ($typeOfBackup == 'files') {
		config()->set('backup.backup.admin_flags', [
			'--disable-notifications' => true,
			'--only-files' => true,
		]);
		config()->set('backup.backup.source.files.include', [
			base_path('.env'),
			storage_path('app/public'),
			storage_path('installed'),
		]);
		config()->set('backup.backup.source.files.exclude', [
			//...
		]);
		config()->set('backup.backup.destination.filename_prefix', 'files-');
	}

	// App files backup
	if ($typeOfBackup == 'app') {
		config()->set('backup.backup.admin_flags', [
			'--disable-notifications' => true,
			'--only-files' => true,
		]);
		config()->set('backup.backup.source.files.include', [
			base_path(),
			// base_path('.gitattributes'),
			base_path('.gitignore'),
		]);
		config()->set('backup.backup.source.files.exclude', [
			base_path('node_modules'),
			base_path('.git'),
			base_path('.idea'),
			base_path('.env'),
			base_path('bootstrap/cache') . DIRECTORY_SEPARATOR . '*',
			public_path('robots.txt'),
			storage_path('app/backup-temp'),
			storage_path('app/database'),
			storage_path('app/public/app/categories/custom') . DIRECTORY_SEPARATOR . '*',
			storage_path('app/public/app/ico') . DIRECTORY_SEPARATOR . '*',
			storage_path('app/public/app/logo') . DIRECTORY_SEPARATOR . '*',
			storage_path('app/public/app/page') . DIRECTORY_SEPARATOR . '*',
			storage_path('app/public/files') . DIRECTORY_SEPARATOR . '*',
			storage_path('app/purifier') . DIRECTORY_SEPARATOR . '*',
			storage_path('database/demo'),
			storage_path('backups'),
			storage_path('dotenv-editor') . DIRECTORY_SEPARATOR . '*',
			storage_path('framework/cache') . DIRECTORY_SEPARATOR . '*',
			storage_path('framework/sessions') . DIRECTORY_SEPARATOR . '*',
			storage_path('framework/testing') . DIRECTORY_SEPARATOR . '*',
			storage_path('framework/views') . DIRECTORY_SEPARATOR . '*',
			storage_path('installed'),
			storage_path('laravel-backups'),
			storage_path('logs') . DIRECTORY_SEPARATOR . '*',
		]);
		config()->set('backup.backup.destination.filename_prefix', 'app-v' . $version . '-');
	}
}

/**
 * Add http:// if it doesn't exists in the URL
 * Recognizes ftp://, ftps://, http:// and https:// in a case insensitive way.
 *
 * @param string|null $url
 * @return string|null
 */
function addHttp(?string $url): ?string
{
	if (!empty($url)) {
		if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
			$url = 'http' . '://' . $url;
		}
	}

	return $url;
}

/**
 * Determine if php is running at the command line
 *
 * @return bool
 */
function isCli(): bool
{
	if (defined('STDIN')) {
		return true;
	}

	if (php_sapi_name() === 'cli') {
		return true;
	}

	if (array_key_exists('SHELL', $_ENV)) {
		return true;
	}

	if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
		return true;
	}

	if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
		return true;
	}

	return false;
}

/**
 * Convert UTF-8 HTML to ANSI
 *
 * https://stackoverflow.com/a/7061511
 * https://onlinehelp.coveo.com/en/ces/7.0/administrator/what_is_the_difference_between_ansi_and_utf-8_uri_formats.htm
 * https://stackoverflow.com/questions/701882/what-is-ansi-format
 *
 * @param string|null $string
 * @return string|null
 */
function convertUTF8HtmlToAnsi(?string $string): ?string
{
	/*
	 * 1. Escaped Unicode characters to HTML hex references. E.g. \u00e9 => &#x00e9;
	 * 2. Convert HTML entities to their corresponding characters. E.g. &#x00e9; => é
	 */
	$string = preg_replace('/\\\\u([a-fA-F0-9]{4})/ui', '&#x\\1;', $string);

	return html_entity_decode($string);
}

/**
 * Remove all non UTF-8 characters
 *
 * Remove Emojis or 4 byte characters.
 * Emojis or BMP character have more than three bytes and maximum of four bytes per character.
 * To store this type of characters, UTF8mb4 character set is needed in MySQL.
 * And it is available only in MySQL 5.5.3 and above versions.
 * Otherwise, remove all 4 byte characters and store it in DB.
 *
 * @param string|null $string
 * @return string|string[]|null
 */
function stripNonUtf(?string $string)
{
	/*
	 * \p{L} matches any kind of letter from any language
	 * \p{N} matches any kind of numeric character in any script (Optional)
	 * \p{M} matches a character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.)
	 * [:ascii:] matches a character with ASCII value 0 through 127
	 */
	return preg_replace('/[^\p{L}\p{N}\p{M}[:ascii:]]+/ui', '', $string);
}

/**
 * @param string $url
 * @param string $string
 * @param int $length
 * @param string $attributes
 * @return string
 */
function linkStrLimit(string $url, string $string, int $length = 0, string $attributes = ''): string
{
	if (!is_string($attributes)) {
		$attributes = '';
	}

	if (!empty($attributes)) {
		$attributes = ' ' . $attributes;
	}

	$tooltip = '';
	if (is_numeric($length) && $length > 0 && str($string)->length() > $length) {
		$tooltip = ' data-bs-toggle="tooltip" title="' . $string . '"';
	}

	$out = '<a href="' . $url . '"' . $attributes . $tooltip . '>';
	if ($length > 0) {
		$out .= str($string)->limit($length);
	} else {
		$out .= $string;
	}
	$out .= '</a>';

	return $out;
}

/**
 * Check if User is online
 *
 * @param $user
 * @return bool
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */
function isUserOnline($user): bool
{
	$user = (is_array($user)) ? Arr::toObject($user) : $user;

	$isOnline = false;

	if (!empty($user) && isset($user->id)) {
		if (config('settings.optimization.cache_driver') == 'array') {
			$isOnline = $user->p_is_online;
		} else {
			$isOnline = cache()->store('file')->has('user-is-online-' . $user->id);
		}
	}

	// Allow only logged users to get the other users status
	$guard = isFromApi() ? 'sanctum' : null;

	return auth($guard)->check() ? $isOnline : false;
}

/**
 * @param string|null $string
 * @return string|null
 */
function nlToBr(?string $string): ?string
{
	// Replace multiple (one or more) line breaks with a single one.
	$string = preg_replace("/[\r\n]+/", "\n", $string);

	return nl2br($string);
}

/**
 * @param string $key
 * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
 */
function dynamicRoute(string $key)
{
	return config($key);
}

/**
 * Set the Db Fallback Locale
 *
 * @param string $fallbackLocale
 */
function setDbFallbackLocale(string $fallbackLocale)
{
	try {
		if (!\Jackiedo\DotenvEditor\Facades\DotenvEditor::keyExists('FALLBACK_LOCALE_FOR_DB')) {
			\Jackiedo\DotenvEditor\Facades\DotenvEditor::addEmpty();
		}
		\Jackiedo\DotenvEditor\Facades\DotenvEditor::setKey('FALLBACK_LOCALE_FOR_DB', $fallbackLocale);
		\Jackiedo\DotenvEditor\Facades\DotenvEditor::save();
	} catch (\Throwable $e) {
	}
}

/**
 * Remove the Db Fallback Locale
 */
function removeDbFallbackLocale()
{
	try {
		if (!\Jackiedo\DotenvEditor\Facades\DotenvEditor::keyExists('FALLBACK_LOCALE_FOR_DB')) {
			\Jackiedo\DotenvEditor\Facades\DotenvEditor::addEmpty();
		}
		\Jackiedo\DotenvEditor\Facades\DotenvEditor::setKey('FALLBACK_LOCALE_FOR_DB', 'null');
		\Jackiedo\DotenvEditor\Facades\DotenvEditor::save();
	} catch (\Throwable $e) {
	}
}

/**
 * Convert only the translations array to json in an array
 *
 * @param array|null $entry
 * @param bool $unescapedUnicode
 * @return array|null
 */
function arrayTranslationsToJson(?array $entry, bool $unescapedUnicode = true): ?array
{
	if (empty($entry)) {
		return $entry;
	}

	$neyEntry = [];
	foreach ($entry as $key => $value) {
		if (is_array($value)) {
			$neyEntry[$key] = ($unescapedUnicode) ? json_encode($value, JSON_UNESCAPED_UNICODE) : json_encode($value);
		} else {
			$neyEntry[$key] = $value;
		}
	}

	return $neyEntry;
}

/**
 * Get Translation from Column (from Json, Array or String)
 *
 * @param $column
 * @param string|null $locale
 * @return false|mixed
 */
function getColumnTranslation($column, string $locale = null)
{
	if (empty($locale)) {
		$locale = app()->getLocale();
	}

	if (!is_array($column)) {
		if (isJson($column)) {
			$column = json_decode($column, true);
		} else {
			$column = [$column];
		}
	}

	return $column[$locale] ?? ($column[config('app.fallback_locale')] ?? head($column));
}

/**
 * SEO Website Verification using meta tags
 * Allow full HTML tag or content="" value
 *
 * @return string
 */
function seoSiteVerification(): string
{
	$engines = [
		'google' => [
			'name' => 'google-site-verification',
			'content' => config('settings.seo.google_site_verification'),
		],
		'bing' => [
			'name' => 'msvalidate.01',
			'content' => config('settings.seo.msvalidate'),
		],
		'yandex' => [
			'name' => 'yandex-verification',
			'content' => config('settings.seo.yandex_verification'),
		],
		'alexa' => [
			'name' => 'alexaVerifyID',
			'content' => config('settings.seo.alexa_verify_id'),
		],
	];

	$out = '';
	foreach ($engines as $engine) {
		if (isset($engine['name'], $engine['content']) && $engine['content']) {
			if (preg_match('|<meta[^>]+>|i', $engine['content'])) {
				$out .= $engine['content'] . "\n";
			} else {
				$out .= '<meta name="' . $engine['name'] . '" content="' . $engine['content'] . '" />' . "\n";
			}
		}
	}

	return $out;
}

/**
 * @param int|null $decimalPlaces
 * @return string
 */
function getInputNumberStep(int $decimalPlaces = null): string
{
	if (empty($decimalPlaces) || $decimalPlaces <= 0) {
		$decimalPlaces = 2;
	}

	return '0.' . (str_pad('1', $decimalPlaces, '0', STR_PAD_LEFT));
}

/**
 * Is 'utf8mb4' is set as the database Charset
 * and 'utf8mb4_unicode_ci' is set as the database collation
 *
 * @return bool
 */
function isUtf8mb4Enabled(): bool
{
	$defaultConnection = config('database.default');
	$databaseCharset = config("database.connections.{$defaultConnection}.charset");
	$databaseCollation = config("database.connections.{$defaultConnection}.collation");

	// Allow Emojis when the database charset is 'utf8mb4'
	// and the database collation is 'utf8mb4_unicode_ci'
	if ($databaseCharset == 'utf8mb4' && $databaseCollation == 'utf8mb4_unicode_ci') {
		return true;
	}

	return false;
}

/**
 * @param string|null $path
 * @return string|string[]
 */
function relativeAppPath(?string $path)
{
	if (isDemoDomain()) {
		$documentRoot = request()->server->get('DOCUMENT_ROOT');
		$path = str_replace($documentRoot, '', $path);

		$basePath = base_path();
		$path = str_replace($basePath, '', $path);

		if (empty($path)) {
			$path = '/';
		}
	}

	return $path;
}

/**
 * @param string|null $url
 * @return string|null
 */
function getFilterClearBtn(?string $url): ?string
{
	$out = '';

	if (!empty($url)) {
		$float = (config('lang.direction') == 'rtl') ? 'left' : 'right';
		$out .= '<a href="' . $url . '" title="' . t('Remove this filter') . '">';
		$out .= '<i class="far fa-window-close" style="float: ' . $float . '; margin-top: 6px; color: #999;"></i>';
		$out .= '</a>';
	}

	return $out;
}

/**
 * Create Random String
 *
 * @param int $length
 * @return string
 */
function createRandomString(int $length = 6): string
{
	$str = '';
	$chars = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
	$max = count($chars) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $chars[$rand];
	}

	return $str;
}

/**
 * Parse the HTTP Accept-Language header
 * NOTE: Get the preferred language: $firstKey = array_key_first($array);
 *
 * @param string|null $acceptLanguage
 * @return array
 */
function parseAcceptLanguageHeader(string $acceptLanguage = null): array
{
	if (empty($acceptLanguage)) {
		$acceptLanguage = request()->server('HTTP_ACCEPT_LANGUAGE');
	}

	$acceptLanguageTab = explode(',', $acceptLanguage);

	$array = [];
	if (!empty($acceptLanguageTab)) {
		foreach ($acceptLanguageTab as $key => $value) {
			$tmp = explode(';', $value);
			if (empty($tmp))
				continue;

			if (isset($tmp[0]) && isset($tmp[1])) {
				$q = str_replace('q=', '', $tmp[1]);
				$array[$tmp[0]] = (double) $q;
			} else {
				$array[$tmp[0]] = 1;
			}
		}
	}
	arsort($array);

	return $array;
}

/**
 * @return bool
 */
function socialLoginIsEnabled(): bool
{
	return (
		config('settings.social_auth.social_login_activation')
		&& (
			(config('settings.social_auth.facebook_client_id') && config('settings.social_auth.facebook_client_secret'))
			|| (config('settings.social_auth.linkedin_client_id') && config('settings.social_auth.linkedin_client_secret'))
			|| (config('settings.social_auth.twitter_client_id') && config('settings.social_auth.twitter_client_secret'))
			|| (config('settings.social_auth.google_client_id') && config('settings.social_auth.google_client_secret'))
		)
	);
}

/**
 * Get Google Maps Embed URL
 * https://developers.google.com/maps/documentation/embed/get-started
 * https://developers.google.com/maps/documentation/embed/embedding-map
 *
 * @param string|null $apiKey
 * @param string|null $q
 * @param string|null $language
 * @return string
 */
function getGoogleMapsEmbedUrl(?string $apiKey, ?string $q, ?string $language = null): string
{
	$baseUrl = 'https://www.google.com/maps/embed/v1/place';

	$query = [
		'key' => $apiKey,
		'q' => $q,
		'zoom' => 9,         // Values ranging from 0 (the whole world) to 21 (individual buildings)
		'maptype' => 'roadmap', // roadmap (default) or satellite
		'language' => $language ?? config('app.locale', 'en'),
	];

	$url = $baseUrl . '?' . Arr::query($query);

	return html_entity_decode($url);
}

/**
 * Get Form Border Radius CSS
 *
 * @param $formBorderRadius
 * @param $fieldsBorderRadius
 * @return string
 */
function getFormBorderRadiusCSS($formBorderRadius, $fieldsBorderRadius): string
{
	$searchFormOptions['form_border_radius'] = $formBorderRadius . 'px';
	$searchFormOptions['fields_border_radius'] = $fieldsBorderRadius . 'px';

	$out = "\n";
	if (config('lang.direction') == 'rtl') {
		$out .= '#homepage .search-row .search-col:first-child .search-col-inner {' . "\n";
		$out .= 'border-top-right-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-right-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col:first-child .form-control {' . "\n";
		$out .= 'border-top-right-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-right-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col .search-btn-border {' . "\n";
		$out .= 'border-top-left-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-left-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col .btn {' . "\n";
		$out .= 'border-top-left-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-left-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
	} else {
		$out .= '#homepage .search-row .search-col:first-child .search-col-inner {' . "\n";
		$out .= 'border-top-left-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-left-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col:first-child .form-control {' . "\n";
		$out .= 'border-top-left-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-left-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col .search-btn-border {' . "\n";
		$out .= 'border-top-right-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-right-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
		$out .= '#homepage .search-row .search-col .btn {' . "\n";
		$out .= 'border-top-right-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= 'border-bottom-right-radius: ' . $searchFormOptions['fields_border_radius'] . ' !important;' . "\n";
		$out .= '}' . "\n";
	}

	$out .= '@media (max-width: 767px) {' . "\n";
	$out .= '#homepage .search-row .search-col:first-child .form-control,' . "\n";
	$out .= '#homepage .search-row .search-col:first-child .search-col-inner,' . "\n";
	$out .= '#homepage .search-row .search-col .form-control,' . "\n";
	$out .= '#homepage .search-row .search-col .search-col-inner,' . "\n";
	$out .= '#homepage .search-row .search-col .btn,' . "\n";
	$out .= '#homepage .search-row .search-col .search-btn-border {' . "\n";
	$out .= 'border-radius: ' . $searchFormOptions['form_border_radius'] . ' !important;' . "\n";
	$out .= '}' . "\n";
	$out .= '}' . "\n";

	return $out;
}

/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 *
 * Supported formats: '#FFF', '#FFFFFF', 'FFF', 'FFFFFF'
 * A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
 *
 * @param string|null $hexCode
 * @param float $percent
 * @return string
 */
function colourBrightness(?string $hexCode, float $percent): string
{
	$hexCode = ltrim($hexCode, '#');

	if (strlen($hexCode) == 3) {
		$hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
	}

	$hexCode = array_map('hexdec', str_split($hexCode, 2));

	foreach ($hexCode as &$color) {
		$adjustableLimit = $percent < 0 ? $color : 255 - $color;
		$adjustAmount = ceil($adjustableLimit * $percent);

		$color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
	}

	return '#' . implode($hexCode);
}

/**
 * Luminosity Contrast algorithm
 * Given a background color, black or white text
 *
 * Will return '#FFFFFF'
 * echo getContrastColor('#FF0000');
 *
 * @param string|null $hexColor
 * @return string
 */
function getContrastColor(?string $hexColor): string
{
	// hexColor RGB
	$r1 = hexdec(substr($hexColor, 1, 2));
	$g1 = hexdec(substr($hexColor, 3, 2));
	$b1 = hexdec(substr($hexColor, 5, 2));

	// Black RGB
	$blackColor = '#000000';
	$rToBlackColor = hexdec(substr($blackColor, 1, 2));
	$gToBlackColor = hexdec(substr($blackColor, 3, 2));
	$bToBlackColor = hexdec(substr($blackColor, 5, 2));

	// Calc contrast ratio
	$l1 = 0.2126 * pow($r1 / 255, 2.2)
		+ 0.7152 * pow($g1 / 255, 2.2)
		+ 0.0722 * pow($b1 / 255, 2.2);

	$l2 = 0.2126 * pow($rToBlackColor / 255, 2.2)
		+ 0.7152 * pow($gToBlackColor / 255, 2.2)
		+ 0.0722 * pow($bToBlackColor / 255, 2.2);

	$contrastRatio = 0;
	if ($l1 > $l2) {
		$contrastRatio = (int) (($l1 + 0.05) / ($l2 + 0.05));
	} else {
		$contrastRatio = (int) (($l2 + 0.05) / ($l1 + 0.05));
	}

	// If contrast is more than 5, return black color
	if ($contrastRatio > 5) {
		return '#000000';
	} else {
		// If not, return white color.
		return '#FFFFFF';
	}
}

/**
 * CSS Minify
 * Note: This works only for CSS code
 *
 * @param string|null $code
 * @return string
 */
function cssMinify(?string $code): string
{
	// Make it into one long line
	$code = str_replace(["\n", "\r"], '', $code);

	// Replace all multiple spaces by one space
	$code = preg_replace('!\s+!', ' ', $code);

	// Replace some unneeded spaces, modify as needed
	$code = str_replace([' {', ' }', '{ ', '; '], ['{', '}', '{', ';'], $code);

	// Remove comments
	$code = str_replace('/*', '_COMMENT_START', $code);
	$code = str_replace('*/', 'COMMENT_END_', $code);
	$code = preg_replace('/_COMMENT_START.*?COMMENT_END_/s', '', $code);

	return trim($code);
}

/**
 * Get package ID request through 'package_id' or 'package'
 *
 * @return mixed|null
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function requestPackageId()
{
	$packageId = null;

	if (request()->filled('package_id')) {
		$packageId = request()->get('package_id');
	}

	if (request()->filled('package') && empty($packageId)) {
		$packageId = request()->get('package');
	}

	if (old('package_id') && empty($packageId)) {
		$packageId = old('package_id');

		if (!empty($packageId) && !request()->has('package_id')) {
			request()->request->add(['package_id' => $packageId]);
		}
	}

	return $packageId;
}

/**
 * Get package by ID
 *
 * @param $packageId
 * @return mixed
 */
function getPackageById($packageId)
{
	$cacheExpiration = (int) config('settings.optimization.cache_expiration');
	$cacheId = 'package.id.' . $packageId . '.' . config('app.locale');

	return cache()->remember($cacheId, $cacheExpiration, function () use ($packageId) {
		return \App\Models\Package::with(['currency'])
			->where('id', $packageId)
			->first();
	});
}

/**
 * Get Listing Pictures Limit
 * For LaraClassifier
 *
 * @param null $model
 * @return int
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function getPicturesLimit($model = null): int
{
	if (empty($model)) {
		$packageId = requestPackageId();
		if (!empty($packageId)) {
			$model = getPackageById($packageId);
			$model = $model->toArray();
		}
	}

	// Default Pictures Limit
	$defaultLimit = 5;
	$picturesLimit = (int) config('settings.single.pictures_limit', $defaultLimit);

	$fromPackagesTable = (array_key_exists('pictures_limit', (array) $model));

	if (!$fromPackagesTable) {
		if (data_get($model, 'featured') == 1) {
			$picturesLimit = (int) data_get($model, 'latestPayment.package.pictures_limit') ?? $picturesLimit;
		}
	} else {
		$picturesLimit = (int) data_get($model, 'pictures_limit') ?? $picturesLimit;
	}

	if ($picturesLimit <= 0)
		$picturesLimit = $defaultLimit;

	return $picturesLimit;
}

/**
 * Get Laravel/cURL HTTP Client Request Error as string
 *
 * @param $response
 * @return string
 */
function getCurlHttpError($response): string
{
	if (is_string($response)) {
		return $response;
	}

	if (
		$response instanceof Throwable
		&& method_exists($response, 'getMessage')
	) {
		$response = $response->getMessage();
	}

	if ($response instanceof \Illuminate\Http\Client\Response) {
		$responseError = null;

		if (method_exists($response, 'reason')) {
			try {
				$responseError = $response->reason();
			} catch (\Exception $e) {
			}
		}

		if (empty($responseError)) {
			if (method_exists($response, 'json')) {
				try {
					$responseError = $response->json();
				} catch (\Exception $e) {
				}
			}
		}

		if (empty($responseError)) {
			if (method_exists($response, 'body')) {
				try {
					$responseError = $response->body();
				} catch (\Exception $e) {
				}
			}
		}

		if (!empty($responseError)) {
			$response = $responseError;
		}
	}

	if (is_array($response)) {
		$response = json_encode($response);
	}

	if (is_string($response)) {
		$response = strip_tags($response);
	}

	if (empty($response) || !is_string($response)) {
		$response = 'Failed to get the request\'s data.';
	}

	return $response;
}

function generateReferenceNumber($length = 5)
{
	// Generate a random string of the specified length
	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, $length);

	// Get the current timestamp
	$timestamp = time();

	// Combine the timestamp and random string to create the reference number
	$referenceNumber = $timestamp . $randomString;

	return $referenceNumber;
}


function nextPaymentDate($database_retrieved_date, $adding_years, $adding_months, $precision)
{

	// ------------------------------------------------------------------------

	// Retrieve the seconds number from the given previous date.
	$database_retrieved_seconds = date("s", strtotime($database_retrieved_date));

	// Retrieve the minutes number from the given previous date.
	$database_retrieved_minutes = date("i", strtotime($database_retrieved_date));

	// Retrieve the hours number from the given previous date.
	$database_retrieved_hours = date("H", strtotime($database_retrieved_date));

	// Retrieve the day number from the given previous date.
	$database_retrieved_day = date("d", strtotime($database_retrieved_date));

	// Retrieve the month number from the given previous date.
	$database_retrieved_month = date("m", strtotime($database_retrieved_date));

	// Retrieve the year number from the given previous date.
	$database_retrieved_year = date("Y", strtotime($database_retrieved_date));

	// ------------------------------------------------------------------------

	// Get the next month number.
	$database_generated_next_month = null;

	// Get the next year number.
	$database_generated_next_year = null;


	if (($database_retrieved_month + $adding_months) > 12) {

		$database_generated_next_month = fmod(($database_retrieved_month + $adding_months), 12);
		$database_generated_next_year = $database_retrieved_year + $adding_years + 1;
	} else {

		$database_generated_next_month = $database_retrieved_month + $adding_months;
		$database_generated_next_year = $database_retrieved_year + $adding_years;
	}

	// Add seconds, minutes and hours if $precision = 1.
	if ($precision) {

		// checkdate(month, day, year).
		if (checkdate($database_generated_next_month, $database_retrieved_day, $database_generated_next_year)) {

			// Generated next month (can be errors in the days)
			$database_generated_next_date = date("Y-m-d H:i:s", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-" . $database_retrieved_day . " " . $database_retrieved_hours . ":" . $database_retrieved_minutes . ":" . $database_retrieved_seconds));

		} else {

			$last_date_of_next_generated_month = date("t", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-1"));

			// Generated next month (can be errors in the days)
			$database_generated_next_date = date("Y-m-d H:i:s", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-" . $last_date_of_next_generated_month . " " . $database_retrieved_hours . ":" . $database_retrieved_minutes . ":" . $database_retrieved_seconds));
		}
	} else {

		// checkdate(month, day, year).
		if (checkdate($database_generated_next_month, $database_retrieved_day, $database_generated_next_year)) {

			// Generated next month (can be errors in the days)
			$database_generated_next_date = date("Y-m-d H:i:s", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-" . $database_retrieved_day . " " . $database_retrieved_hours . ":" . $database_retrieved_minutes . ":" . $database_retrieved_seconds));

		} else {

			$last_date_of_next_generated_month = date("t", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-1"));

			// Generated next month (can be errors in the days)
			$database_generated_next_date = date("Y-m-d H:i:s", strtotime($database_generated_next_year . "-" . $database_generated_next_month . "-" . $last_date_of_next_generated_month . " 00:00:00"));
		}

	}

	return $database_generated_next_date;

}

function nextPaymentDay($database_retrieved_date, $adding_dates, $precision)
{

	// ------------------------------------------------------------------------

	// Retrieve the day number from the given previous date.
	$database_retrieved_day = date("d", strtotime($database_retrieved_date));

	// Retrieve the month number from the given previous date.
	$database_retrieved_month = date("m", strtotime($database_retrieved_date));

	// Retrieve the year number from the given previous date.
	$database_retrieved_year = date("Y", strtotime($database_retrieved_date));

	// Retrieve the H:i:s from the given previous date.
	$database_retrieved_time = date("H:i:s", strtotime($database_retrieved_date));

	// ------------------------------------------------------------------------

	$total_dates = $database_retrieved_day + $adding_dates;

	$last_date_of_next_generated_month = date("t", strtotime($database_retrieved_date));

	$adding_months = intdiv($total_dates, $last_date_of_next_generated_month);

	$next_month_dates = fmod($total_dates, $last_date_of_next_generated_month);

	// Replase given date with added dates.
	$database_generated_next_day = date("Y-m-d H:i:s", strtotime($database_retrieved_year . "-" . $database_retrieved_month . "-" . $next_month_dates . " " . $database_retrieved_time));

	if ($adding_months > 0) {

		$database_generated_next_day = nextPaymentDate($database_generated_next_day, 0, $adding_months, $precision);

	}

	return $database_generated_next_day;

}

function split_name($name)
{

	$name = trim($name);

	$last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);

	$first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

	return array($first_name, $last_name);
}

function stringToArray($string)
{

	$components = preg_split("/[|]/", $string);

	$array = [];

	foreach ($components as $component) {

		$arrayItem = preg_split("/[:]/", $component);

		$array[$arrayItem[0]] = $arrayItem[1];

	}

	return $array;

}

function ArrayToString($array)
{

	$string = "";

	foreach ($array as $key => $value) {

		$string .= $key . ":" . $value . "|";

	}

	$string = substr($string, 0, -1);

	return $string;

}

function generatePositionText($number)
{
	// Check if the number is greater than 0
	if ($number <= 0) {
		return "Invalid position";
	}

	// Get the last digit of the number
	$lastDigit = $number % 10;

	// Check for special cases (11th, 12th, 13th)
	if ($number >= 11 && $number <= 13) {
		return $number . "th";
	}

	// Determine the suffix based on the last digit
	switch ($lastDigit) {
		case 1:
			return $number . "st";
		case 2:
			return $number . "nd";
		case 3:
			return $number . "rd";
		default:
			return $number . "th";
	}
}

function str_slug($string, $separator = "-")
{
	return Str::slug($string, $separator);
}

function reverse_slug($slug, $separator = "-")
{
	return str_replace($separator, " ", $slug);
}