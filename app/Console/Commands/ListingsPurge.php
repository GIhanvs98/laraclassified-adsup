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

namespace App\Console\Commands;

// Increase the server resources
$iniConfigFile = __DIR__ . '/../../Helpers/Functions/ini.php';
if (file_exists($iniConfigFile)) {
	include_once $iniConfigFile;
}

use App\Models\Package;
use App\Models\Payment;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\ReviewedScope;
use App\Notifications\PostArchived;
use App\Notifications\PostDeleted;
use App\Notifications\PostWilBeDeleted;
use App\Models\Post;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ListingsPurge extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'listings:purge';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete all old Listings.';
	
	/**
	 * Default Listing Expiration Duration
	 *
	 * @var int
	 */
	private int $unactivatedPostsExpiration = 30;       // Delete the unactivated Posts after this expiration
	private int $activatedPostsExpiration = 30;         // Archive the activated Posts after this expiration
	private int $archivedPostsExpiration = 7;           // Delete the archived Posts after this expiration
	private int $manuallyArchivedPostsExpiration = 90;  // Delete the manually archived Posts after this expiration
	
	/**
	 * ListingsCleaner constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->unactivatedPostsExpiration = (int)config('settings.cron.unactivated_listings_expiration', $this->unactivatedPostsExpiration);
		$this->activatedPostsExpiration = (int)config('settings.cron.activated_listings_expiration', $this->activatedPostsExpiration);
		$this->archivedPostsExpiration = (int)config('settings.cron.archived_listings_expiration', $this->archivedPostsExpiration);
		$this->manuallyArchivedPostsExpiration = (int)config('settings.cron.manually_archived_listings_expiration', $this->manuallyArchivedPostsExpiration);
	}
	
	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		if (isDemoDomain(env('APP_URL'))) {
			$msg = t('demo_mode_message');
			(isCli()) ? $this->warn($msg) : $this->printWeb($msg);
			exit();
		}
		
		// Get all countries
		$countries = Country::withoutGlobalScope(ActiveScope::class);
		if ($countries->doesntExist()) {
			$msg = 'No country found.';
			(isCli()) ? $this->warn($msg) : $this->printWeb($msg);
			exit();
		}
		
		// Get the default/current locale
		$defaultLocale = app()->getLocale();
		
		// Browse countries
		foreach ($countries->cursor() as $country) {
			
			// Get the country locale
			$countryLocale = getCountryMainLangCode(collect($country));
			if (empty($countryLocale) || !is_string($countryLocale)) {
				$countryLocale = $defaultLocale;
			}
			
			// Set the country locale
			config()->set('app.locale', $countryLocale);
			app()->setLocale($countryLocale);
			
			// Get country's (non-permanent) items
			$posts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->countryOf($country->code)
				->where(function ($query) {
					$query->columnIsEmpty('is_permanent')
						->whereHas('category', function ($q) {
							$q->columnIsEmpty('is_for_permanent');
						});
				});
			
			if ($posts->doesntExist()) {
				$msg = 'No listings in "' . $country->name . '" (' . strtoupper($country->code) . ') website.';
				(isCli()) ? $this->info($msg) : $this->printWeb($msg);
				
				continue;
			}
			
			/*
			 * Items Processing (Using Eloquent Cursor Method)
			 * The cursor method allows you to iterate through your database records using a cursor, which will only execute a single query.
			 * When processing large amounts of data, the cursor method may be used to greatly reduce your memory usage
			 */
			foreach ($posts->cursor() as $post) {
				try {
					$this->itemProcessing($post, $country);
				} catch (\Throwable $e) {
					dd($e);
				}
			}
			
		}
		
		$msg = 'END.';
		(isCli()) ? $this->info($msg) : $this->printWeb($msg);
	}
	
	/**
	 * @param \App\Models\Post $post
	 * @param \App\Models\Country $country
	 * @throws \Exception
	 */
	private function itemProcessing(Post $post, Country $country)
	{
		// Debug
		// if ($country->code != 'US') return;
		
		// Get the Country's TimeZone
		$timeZone = (isset($country->time_zone) && !empty($country->time_zone))
			? $country->time_zone
			: config('app.timezone');
		
		// Get the current Datetime
		$today = Carbon::now($timeZone);
		
		// Debug
		// dd($today->diffInDays($post->created_at));
		
		/* For non-activated items */
		if (!isVerifiedPost($post)) {
			// Delete non-active items after '$this->unactivatedPostsExpiration' days
			if ($today->diffInDays($post->created_at) >= $this->unactivatedPostsExpiration) {
				$post->delete();
			}
			
			/*
			 * IMPORTANT
			 * Break: Non-activated item expected treatment applied
			 */
			
			return;
		}
		
		/* For activated items */
		// Get all packages (Just count them)
		$packages = Package::query();
		
		/* Is it a website with premium options enabled? */
		$payment = null;
		$package = null;
		if ($packages->count() > 0) {
			// Check the item's transactions (Get the last transaction (Non pushed))
			$payment = Payment::where('post_id', $post->id)
				->where(function ($query) {
					$query->where('transaction_id', '!=', 'featured')->orWhereNull('transaction_id');
				})
				->orderByDesc('id')
				->first();
			if (!empty($payment)) {
				// Get package info
				$package = Package::find($payment->package_id);
				if (!empty($package)) {
					if (!empty($package->duration)) {
						$this->activatedPostsExpiration = $package->duration;
					}
				}
			}
		}
		
		/* Check if the item is premium|featured */
		if ($post->featured == 1) {
			if (!empty($payment) && !empty($package)) {
				// Un-featured the item after {$package->promo_duration} days (related to the Payment date)
				if ($today->diffInDays($payment->created_at) >= $package->promo_duration) {
					
					// Un-featured
					$post->featured = 0;
					$post->save();
					
				}
				
				/*
				 * IMPORTANT
			 	 * Break: Premium|featured item expected treatment applied
			 	 */
				
				return;
			}
			
			/*
			 * Payment or package not found. Apply non-premium|non-featured treatment.
			 */
		}
		
		/* For non-archived items (Not to be confused with "non-activated items") */
		// Auto-archive
		if (empty($post->archived_at)) {
			// Archive all activated listings after '$this->activatedPostsExpiration' days
			if ($today->diffInDays($post->created_at) >= $this->activatedPostsExpiration) {
				// Archive
				$post->archived_at = $today;
				$post->save();
				
				if ($country->active == 1) {
					try {
						// Send Notification Email to the Author
						$post->notify(new PostArchived($post, $this->archivedPostsExpiration));
					} catch (\Throwable $e) {
						$msg = $e->getMessage() . PHP_EOL;
						(isCli()) ? $this->warn($msg) : $this->printWeb($msg);
					}
				}
			}
			
			/*
			 * IMPORTANT
			 * Break: Non-archived item expected treatment applied
			 */
			
			return;
		}
		
		/* For archived items (Not to be confused with "activated items") */
		// Auto-delete
		// $today = $today->addDays(4); // Debug
		
		// Count days since the item has been archived
		$countDaysSinceAdHasBeenArchived = $today->diffInDays($post->archived_at);
		
		// Send one alert email each X day started from Y days before the final deletion until the item deletion (using 'archived_at')
		// Start alert email sending from 7 days earlier (for example)
		$daysEarlier = 7;       // In days (Y)
		$intervalOfSending = 2; // In days (X)
		
		if (empty($post->archived_manually_at)) {
			$archivedPostsExpirationSomeDaysEarlier = $this->archivedPostsExpiration - $daysEarlier;
		} else {
			$archivedPostsExpirationSomeDaysEarlier = $this->manuallyArchivedPostsExpiration - $daysEarlier;
		}
		
		if ($countDaysSinceAdHasBeenArchived >= $archivedPostsExpirationSomeDaysEarlier) {
			// Update the '$daysEarlier' to show in the mail
			$daysEarlier = $daysEarlier - $countDaysSinceAdHasBeenArchived;
			
			if ($daysEarlier > 0) {
				// Using 'deletion_mail_sent_at'
				if (empty($post->deletion_mail_sent_at) || $today->diffInDays($post->deletion_mail_sent_at) >= $intervalOfSending) {
					try {
						$post->notify(new PostWilBeDeleted($post, $daysEarlier));
					} catch (\Throwable $e) {
						$msg = $e->getMessage() . PHP_EOL;
						(isCli()) ? $this->warn($msg) : $this->printWeb($msg);
					}
					
					// Update the field 'deletion_mail_sent_at' with today timestamp
					$post->deletion_mail_sent_at = $today;
					$post->save();
				}
			}
		}
		
		// Delete all archived item '$this->archivedPostsExpiration' days later (using 'archived_at')
		if ($countDaysSinceAdHasBeenArchived >= $this->archivedPostsExpiration) {
			if ($country->active == 1) {
				try {
					// Send Notification Email to the Author
					$post->notify(new PostDeleted($post));
				} catch (\Throwable $e) {
					$msg = $e->getMessage() . PHP_EOL;
					(isCli()) ? $this->warn($msg) : $this->printWeb($msg);
				}
			}
			
			// Delete
			$post->delete();
		}
		
		/*
		 * IMPORTANT
		 * Break: Archived item expected treatment applied
		 */
	}
	
	/**
	 * @param $var
	 */
	private function printWeb($var)
	{
		// Only for Debug !
		// echo '<pre>'; print_r($var); echo '</pre>';
	}
}
