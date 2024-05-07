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
						<h2 class="title-2"><i class="bi bi-person-check"></i> Ads Reports </h2>
						
						<div style="clear:both"></div>

                        <livewire:account-report-ads :user="auth()->user()" />
						
						<div style="clear:both"></div>
					
					</div>
				</div>
				
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
@endsection
