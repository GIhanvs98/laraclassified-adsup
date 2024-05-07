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

namespace App\Helpers\Search\Traits\Relations;

use App\Models\Package;
use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait PaymentRelation
{
	protected function setPaymentRelation(): void
	{
		if (!(isset($this->posts) && isset($this->postsTable) && isset($this->groupBy))) {
			abort(500, 'Fatal Error: Payment relation cannot be applied.');
		}
		
		// latestPayment
		$this->posts->with('latestPayment', fn($query) => $query->with('package'));
		
		// latestPayment (Can be used in orderBy)
		$tablesPrefix = DB::getTablePrefix();
		
		$select = [];
		$select[] = $tablesPrefix . 'tPackage.lft';
		if (self::$dbModeStrict) {
			$this->groupBy[] = 'tPackage.lft';
		}
		
		$this->posts->addSelect(DB::raw(implode(', ', $select)));
		
		$paymentsTable = (new Payment())->getTable();
		$packagesTable = (new Package())->getTable();
		
		$latestPaymentBuilder = $this->getLatestPaymentBuilder($tablesPrefix, $paymentsTable);
		
		$op = data_get($this->input, 'op');
		
		if ($op == 'premium') {
			$displayFreeInPremium = (config('settings.list.free_listings_in_premium') == '1');
			if ($displayFreeInPremium) {
				$this->setRelationForPremiumFirst($paymentsTable, $packagesTable, $latestPaymentBuilder);
			} else {
				$this->setRelationForPremium($paymentsTable, $packagesTable, $latestPaymentBuilder);
			}
		} else if ($op == 'latest') {
			$this->setRelationForLatest($paymentsTable, $packagesTable, $latestPaymentBuilder);
		} else if ($op == 'free') {
			$this->setRelationForFree();
		} else if ($op == 'premiumFirst') {
			$this->setRelationForPremiumFirst($paymentsTable, $packagesTable, $latestPaymentBuilder);
		} else {
			// For op == 'search' and others
			$displayPremiumFirst = (
				(config('settings.list.premium_first') == '1' && empty($this->cat) && empty($this->city))
				|| (config('settings.list.premium_first_category') == '1' && !empty($this->cat))
				|| (config('settings.list.premium_first_location') == '1' && !empty($this->city))
			);
			
			if ($displayPremiumFirst) {
				$this->setRelationForPremiumFirst($paymentsTable, $packagesTable, $latestPaymentBuilder);
			} else {
				$this->setRelationForLatest($paymentsTable, $packagesTable, $latestPaymentBuilder);
			}
		}
	}
	
	private function getLatestPaymentBuilder($tablesPrefix, $paymentsTable): \Illuminate\Database\Query\Builder
	{
		return DB::table($paymentsTable, 'lp')
			->select(DB::raw('MAX(' . $tablesPrefix . 'lp.id) as lpId'), 'lp.post_id')
			->where('lp.active', 1)
			->groupBy('lp.post_id');
	}
	
	/*
	 * The standard way:
	 * Select the premium listings first (sorted by their package order)
	 */
	private function setRelationForPremiumFirst($paymentsTable, $packagesTable, $latestPaymentBuilder): void
	{
		$this->posts->leftJoinSub($latestPaymentBuilder, 'tmpLp', function ($join) {
			$join->on('tmpLp.post_id', '=', $this->postsTable . '.id')->where('featured', 1);
		});
		$this->posts->leftJoin($paymentsTable . ' as latestPayment', 'latestPayment.id', '=', 'tmpLp.lpId');
		$this->posts->leftJoin($packagesTable . ' as tPackage', 'tPackage.id', '=', 'latestPayment.package_id');
		
		// Priority to the Premium Listings
		// Push the Package Position order onto the beginning of an array
		// Check out the orderBy items positions in the OrderBy file
		$this->orderBy = Arr::prepend($this->orderBy, 'tPackage.lft DESC');
	}
	
	/*
	 * Select only the premium listings (sorted by their package order)
	 */
	private function setRelationForPremium($paymentsTable, $packagesTable, $latestPaymentBuilder): void
	{
		$this->posts->joinSub($latestPaymentBuilder, 'tmpLp', function ($join) {
			$join->on('tmpLp.post_id', '=', $this->postsTable . '.id')->where('featured', 1);
		});
		$this->posts->join($paymentsTable . ' as latestPayment', 'latestPayment.id', '=', 'tmpLp.lpId');
		$this->posts->join($packagesTable . ' as tPackage', 'tPackage.id', '=', 'latestPayment.package_id');
		
		// Priority to the Premium Listings
		// Push the Package Position order onto the beginning of an array
		$this->orderBy = Arr::prepend($this->orderBy, 'tPackage.lft DESC');
	}
	
	/*
	 * Select latest listings (including premium & normal listings)
	 * Sorted by the listings' creation date: 'created_at'
	 */
	private function setRelationForLatest($paymentsTable, $packagesTable, $latestPaymentBuilder): void
	{
		$this->posts->leftJoinSub($latestPaymentBuilder, 'tmpLp', function ($join) {
			$join->on('tmpLp.post_id', '=', $this->postsTable . '.id')->where('featured', 1);
		});
		$this->posts->leftJoin($paymentsTable . ' as latestPayment', 'latestPayment.id', '=', 'tmpLp.lpId');
		$this->posts->leftJoin($packagesTable . ' as tPackage', 'tPackage.id', '=', 'latestPayment.package_id');
	}
	
	/*
	 * Free (Not premium) listings
	 */
	private function setRelationForFree(): void
	{
		$this->posts->where('featured', '!=', 1);
	}
}
