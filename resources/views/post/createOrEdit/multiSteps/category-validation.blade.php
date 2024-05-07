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

@section('wizard')
	@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.multiSteps.inc.wizard', 'post.createOrEdit.multiSteps.inc.wizard'])
@endsection

@php
	$postInput ??= [];
	
	$postTypes ??= [];
	$countries ??= [];
@endphp

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container" style="padding-top: 20px;">
		<div class="container">
			<div class="row">
				
				@includeFirst([config('larapen.core.customizedViewPath') . 'post.inc.notification', 'post.inc.notification'])
				
				<div class="col-md-12 page-content max-w-screen-lg">
					<div class="inner-box category-content bg-white" style="overflow: visible;">
						
						<div id="post-limit-warning">

							<!-- To Sell something or Looking for Buy/Rent: -->
							<h2 class="title-2 text-center text-gray-700 text-2xl border-0 mt-[30px] font-light">
								<strong>Dear {{ auth()->user()->name }}, You have exceeded your post limit for <span class="category-name" style="font-weight: inherit;" ></span> category.</strong>
							</h2>
							<p class="mb-[30px] w-full text-center">Please upgrade your membership.</p>
							
						</div>

					</div>
				</div>
				<!-- /.page-content -->
				
			</div>
		</div>
	</div>
	@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.category-modal', 'post.createOrEdit.inc.category-modal'])
@endsection


@section('after_scripts')
@endsection

@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.form-assets', 'post.createOrEdit.inc.form-assets'])
