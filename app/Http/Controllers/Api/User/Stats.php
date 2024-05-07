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

namespace App\Http\Controllers\Api\User;

use App\Models\Payment;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Transaction;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Thread;
use Illuminate\Support\Facades\DB;

trait Stats
{
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getStats($id): \Illuminate\Http\JsonResponse
	{
		$posts = [];
		$threads = [];
		
		// posts (published)
		$posts['published'] = Post::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->verified()
			->unarchived()
			->reviewed()
			->count();
		
		// posts (pendingApproval)
		$posts['pendingApproval'] = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
			->whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->unverified()
			->count();
		
		// posts (archived)
		$posts['archived'] = Post::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->archived()
			->count();
		
		// posts (visits)
		$postsVisits = DB::table((new Post())->getTable())
			->select('user_id', DB::raw('SUM(visits) as totalVisits'))
			->where('country_code', config('country.code'))
			->where('user_id', $id)
			->groupBy('user_id')
			->first();
		$posts['visits'] = $postsVisits->totalVisits ?? 0;
		
		// posts (favourite)
		$posts['favourite'] = SavedPost::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry();
		})->where('user_id', $id)
			->count();
		
		// savedSearch
		$savedSearch = SavedSearch::whereHas('country')
			->currentCountry()
			->where('user_id', $id)
			->count();
		
		// threads (all)
		$threads['all'] = Thread::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry()
				->unarchived();
		})->forUser($id)->count();
		
		// threads (withNewMessage)
		$threads['withNewMessage'] = Thread::whereHas('post', function ($query) {
			$query->whereHas('country')
				->currentCountry()
				->unarchived();
		})->forUserWithNewMessages($id)->count();
		
		// transactions (payments)
		$transactions = auth()->user()->transactions()->onepayTransactions()->count();

		// stats
		$stats = [
			'posts'        => $posts,
			'savedSearch'  => $savedSearch,
			'threads'      => $threads,
			'transactions' => $transactions,
		];
		
		$data = [
			'success' => true,
			'message' => null,
			'result'  => $stats,
		];
		
		return $this->apiResponse($data);
	}
}
