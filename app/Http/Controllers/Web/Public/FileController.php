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

namespace App\Http\Controllers\Web\Public;

use App\Helpers\Files\Response\FileContentResponseCreator;
use App\Helpers\Files\Storage\StorageDisk;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Public\Traits\HasIntlTelInput;

class FileController extends Controller
{
	use HasIntlTelInput;
	
	protected $disk;
	
	/**
	 * FileController constructor.
	 */
	public function __construct()
	{
		$diskName = null;
		
		if (request()->has('disk')) {
			$tmpDiskName = request()->get('disk');
			$allowedNames = ['private', 'public'];
			if (config('filesystems.disks.' . $tmpDiskName) && in_array($tmpDiskName, $allowedNames)) {
				$diskName = $tmpDiskName;
			}
		}
		
		if ($diskName == 'private') {
			$this->middleware('auth')->only(['show']);
		}
		
		$this->disk = StorageDisk::getDisk($diskName);
	}
	
	/**
	 * Get & watch media file (image, audio & video) content
	 *
	 * @param \App\Helpers\Files\Response\FileContentResponseCreator $response
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse|null
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function watchMediaContent(FileContentResponseCreator $response)
	{
		$filePath = request()->get('path');
		$filePath = preg_replace('|\?.*|ui', '', $filePath);
		
		$out = $response::create($this->disk, $filePath);
		
		ob_end_clean(); // HERE IS THE MAGIC
		
		return $out;
	}
	
	/**
	 * Translation of the bootstrap-fileinput plugin
	 *
	 * @param string $code
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
	 */
	public function bootstrapFileinputLocales(string $code = 'en')
	{
		$fileInputArray = trans('fileinput', [], $code);
		if (is_array($fileInputArray) && !empty($fileInputArray)) {
			if (config('settings.optimization.minify_html_activation') == 1) {
				$fileInputJson = json_encode($fileInputArray, JSON_UNESCAPED_UNICODE);
			} else {
				$fileInputJson = json_encode($fileInputArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			}
			
			if (!empty($fileInputJson)) {
				// $fileInputJson = str_replace('<\/', '</', $fileInputJson);
				$out = '(function ($) {' . "\n";
				$out .= '"use strict";' . "\n\n";
				$out .= "$.fn.fileinputLocales['$code'] = ";
				$out .= $fileInputJson . ';' . "\n";
				$out .= '})(window.jQuery);' . "\n";
				
				return response($out, 200, ['Content-Type' => 'application/javascript']);
			}
		}
		
		$filePath = public_path('assets/plugins/bootstrap-fileinput/js/locales/' . ietfLangTag(config('app.locale')) . '.js');
		if (file_exists($filePath)) {
			$out = file_get_contents($filePath);
			
			return response($out, 200, ['Content-Type' => 'application/javascript']);
		}
		
		abort(404, 'File not found!');
	}
	
	/**
	 * Generate Skin & Custom CSS
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function cssStyle()
	{
		$display = request()->get('display');
		$display = is_string($display) ? $display : null;
		
		$out = '';
		
		$hOut = '/* === CSS Version === */' . "\n";
		$hOut .= '/* === v' . config('app.appVersion') . ' === */' . "\n";
		
		try {
			$out .= view('common.css.style', ['disk' => $this->disk])->render();
			$out .= view('common.css.ribbons', ['disk' => $this->disk, 'display' => $display])->render();
			$out = preg_replace('|</?style[^>]*>|i', '', $out);
		} catch (\Throwable $e) {
			$out .= '/* === CSS Error Found === */' . "\n";
		}
		
		$out = cssMinify($out);
		
		$out = $hOut . $out;
		
		return response($out, 200, ['Content-Type' => 'text/css']);
	}
}
