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

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExceptionOccurred extends Notification
{
	public array $content;
	
	public function __construct(array $content)
	{
		$this->content = $content;
	}
	
	public function via($notifiable)
	{
		return ['mail'];
	}
	
	public function toMail($notifiable)
	{
		$default = '--';
		
		$mailMessage = (new MailMessage);
		$mailMessage->subject('ErrorException on ' . config('app.name'));
		
		// The Error
		$mailMessage->greeting('Error:');
		$errorMessage = $this->content['message'] ?? $default;
		$file = $this->content['file'] ?? $default;
		$line = $this->content['line'] ?? $default;
		$mailMessage->line($errorMessage);
		$mailMessage->line('in <strong>' . $file . '</strong> line <strong>' . $line . '</strong>');
		
		// The Request
		$mailMessage->line('<br><h4>----- Request -----</h4>');
		$ipLink = (isset($this->content['ip']))
			? config('larapen.core.ipLinkBase') . $this->content['ip']
			: $default;
		$reqOut = '<strong>Method:</strong> ' . $this->content['method'] ?? $default;
		$reqOut .= '<br><strong>URL:</strong> ' . $this->content['url'] ?? $default;
		$reqOut .= '<br><strong>IP:</strong> <a href="' . $ipLink . '" target="_blank">' . $ipLink . '</a>';
		$reqOut .= '<br><strong>User agent:</strong> ' . $this->content['userAgent'] ?? $default;
		$reqOut .= '<br><strong>Referer:</strong> ' . $this->content['referer'] ?? $default;
		$mailMessage->line($reqOut);
		
		// The Trace
		$mailMessage->line('<br><h4>----- Trace -----</h4>');
		$traceOut = '';
		$trace = $this->content['trace'] ?? [];
		foreach($trace as $value) {
			$class = $value['class'] ?? $default;
			$function = $value['function'] ?? $default;
			$file = $value['file'] ?? $default;
			$line = $value['line'] ?? $default;
			
			if (!empty($traceOut)) {
				$traceOut .= '<br>';
			}
			
			$traceOut .= 'at <span title="' . $class . '">' . basename($class) . '</span>->' . $function . '() in <strong>' . $file . '</strong> line ' . $line;
		}
		if (empty($traceOut)) {
			$traceOut .= '...';
		}
		$mailMessage->line($traceOut);
		
		// Team Salutation
		$mailMessage->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
		
		return $mailMessage;
	}
}
