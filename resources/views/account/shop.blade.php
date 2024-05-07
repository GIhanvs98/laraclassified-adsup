{{--
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
--}}
@extends('layouts.master')

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>
				
				<div class="col-md-9 page-content">
					<div class="inner-box">
						<h2 class="title-2 w-full flex justify-between" style="padding-bottom: 14px;">
							<div class="w-fit flex align-center">
								<i class="bi bi-shop" style="padding-right: 8px;"></i> 
								Shop 
							</div>
							@if (\App\Models\Shop::where('user_id', auth()->user()->id)->exists())
								<a href="{{ route('shops.index', ['id'=> auth()->user()->shop->id, 'slug'=> \Illuminate\Support\Str::slug(auth()->user()->shop->title, '-')]) }}" title="Open my shop">
									<button type="button" class="btn btn-dark">Open Shop</button>
								</a>
							@endif
						</h2>
						
						<div style="clear:both"></div>

							@if($membershipDoesntExist)
								<div style="display: flex; justify-content: center; align-items: center; padding: 0px 12px 12px 12px;">
									You do not have a membership. Please purchase a membership to obtain this feature.
								</div>
							@else
								@if($transactionsValidDoesntExist)
									<div style="display: flex; justify-content: center; align-items: center; padding: 0px 12px 12px 12px;">
										Your payment is overdue. Please renew your membership to avoid service disruption.
									</div>
								@else
									{{-- Members's Shop --}}
									<livewire:update-shop />
								@endif
							@endif
		
						<div style="clear:both"></div>
					
					</div>
				</div>
				
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
@endsection
