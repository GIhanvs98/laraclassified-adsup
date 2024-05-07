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

                {{--<div class="inner-box">
                    <h2 class="title-2"><i class="fas fa-coins"></i> {{ t('Transactions') }} </h2>

                    <div style="clear:both"></div>

                    <div class="table-responsive">

                        @if (auth()->user()->transactions()->onepayTransactions()->exists())

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><span>ID</span></th>
                                    <th>{{ t('Description') }}</th>
                                    <th>{{ t('Payment Method') }}</th>
                                    <th>{{ t('Value') }}</th>
                                    <th>{{ t('Date') }}</th>
                                    <th>{{ t('Status') }}</th>
                                    <th>Valid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $key => $transaction)
                                    <tr class="bg-white border-b">
                                        <td scope="row" class="p-3">
                                            {{ $key+1 }}
                                        </td>
                                        <td class="p-3 min-w-max">
                                            @if ($transaction->payment_type == "membership")
                                            <strong>`{{ ucfirst($transaction->membership->name) }}`</strong> membership<br>
                                            @endif

                                            @if ($transaction->payment_type == "ad-promotion")

                                                @if($transaction->post()->exists())
                                                    <a href="{{ \App\Helpers\UrlGen::post($transaction->post) }}">{{ $transaction->post->title }}</a><br>
                                                @endif
                                            @endif

                                            <div style="font-size: 10px; margin-top: 4px;">
                                                {{ t('type') }} - {{ ucfirst($transaction->payment_type) }} payment<br>
                                                @isset($transaction->payment_valid_untill_datetime)
                                                Valid untill - {{ $transaction->payment_valid_untill_datetime }} {{ t('days') }}
                                                @endisset
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            {{ ucfirst($transaction->payment_method) }}
                                        </td>
                                        <td class="p-3">
                                            {!! $transaction->currency->symbol .".". $transaction->net_amount !!}
                                        </td>
                                        <td class="p-3">
                                            {{ date_format(date_create($transaction->payment_started_datetime),"Y-m-d") }}
                                            &nbsp;at&nbsp;
                                            {{ date_format(date_create($transaction->payment_started_datetime),"H:i:s") }}
                                        </td>
                                        <td class="p-3">
                                            @if ($transaction->payment_status == "success")
                                            <span class="badge bg-success">{{ ucfirst($transaction->payment_status) }}</span>
                                            @else
                                            <span class="badge bg-info">{{ ucfirst($transaction->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            @if ($transaction->active == 1)
                                            <span class="badge bg-success">Valid</span>
                                            @else
                                            <span class="badge bg-info">{{ t('Expired') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @else
                        <div class="text-center mt10 mb30">You haven't done any transactions yet.</div>
                        @endif

                    </div>

                    <nav>
                        @include('vendor.pagination.api.bootstrap-4')
                    </nav>

                    <div style="clear:both"></div>

                </div>--}}


                <div class="relative w-full sm:rounded-sm bg-white">
                    <div class="px-6 py-3 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white" style="border-radius: 0.25rem 0.25rem;">
                        <div class="mb-2 text-xl">
                            <i class="fas fa-coins"></i> {{ t('Transactions') }}
                        </div>
                        @if (!empty($transactions))
                            <div class="block">
                                <label for="filter" class="sr-only">Search</label>
                                <div class="relative" style="margin: 20px 0px 0px !important;">
                                    <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <input type="text" id="filter" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for ads">
                                </div>
                            </div>
                        @endif
                    </div>

                    @if (empty($transactions))
                        <div class="px-6 pb-3 text-left text-xs text-gray-600">
                            You haven't done any transactions yet.
                        </div>
                    @else
                        <div class="overflow-x-auto w-full" style="border-radius: 0.25rem 0.25rem;">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500"  id="addManageTable" data-filter="#filter" data-filter-text-only="true">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="p-3" data-type="numeric" data-sort-initial="true"><span>ID</span></th>
                                        <th scope="col" class="p-3" data-sort-ignore="true">{{ t('Description') }}</th>
                                        <th scope="col" class="p-3">{{ t('Payment Method') }}</th>
                                        <th scope="col" class="p-3" data-type="numeric">{{ t('Value') }}</th>
                                        <th scope="col" class="p-3" data-type="numeric">{{ t('Date') }}</th>
                                        <th scope="col" class="p-3">{{ t('Status') }}</th>
                                        <th scope="col" class="p-3">Valid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $key => $transaction)
                                        <tr class="bg-white border-b">
                                            <td scope="row" class="p-3">
                                                {{ $key+1 }}
                                            </td>
                                            <td class="p-3 min-w-max">
                                                @if ($transaction->payment_type == "membership")
                                                <strong>`{{ ucfirst($transaction->membership->name) }}`</strong> membership<br>
                                                @endif

                                                @if ($transaction->payment_type == "ad-promotion")

                                                    @if($transaction->post()->exists())
                                                        <a href="{{ \App\Helpers\UrlGen::post($transaction->post) }}">{{ $transaction->post->title }}</a><br>
                                                    @endif
                                                @endif

                                                <div style="font-size: 10px; margin-top: 4px;">
                                                    {{ t('type') }} - {{ ucfirst($transaction->payment_type) }} payment<br>
                                                    @isset($transaction->payment_valid_untill_datetime)
                                                    Valid untill - {{ $transaction->payment_valid_untill_datetime }} {{ t('days') }}
                                                    @endisset
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                {{ ucfirst($transaction->payment_method) }}
                                            </td>
                                            <td class="p-3">
                                                {!! $transaction->currency->symbol .".". $transaction->net_amount !!}
                                            </td>
                                            <td class="p-3 text-xs">
                                                {{ date_format(date_create($transaction->payment_started_datetime),"Y-m-d") }}
                                                &nbsp;at&nbsp;
                                                {{ date_format(date_create($transaction->payment_started_datetime),"H:i:s") }}
                                            </td>
                                            <td class="p-3">
                                                @if ($transaction->payment_status == "success")
                                                <span class="badge bg-success">{{ ucfirst($transaction->payment_status) }}</span>
                                                @else
                                                <span class="badge bg-info">{{ ucfirst($transaction->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                @if ($transaction->active == 1)
                                                <span class="badge bg-success">Valid</span>
                                                @else
                                                <span class="badge bg-info">{{ t('Expired') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <nav>
                                @include('vendor.pagination.api.bootstrap-4')
                            </nav>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</div>
@endsection


@section('after_styles')

	{{-- Flowbite css --}}
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

	{{-- Flowbite js --}}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

	<style>
		.action-td p {
			margin-bottom: 5px;
		}

		.collapse {
			visibility: initial;
		}

		.sr-only{
		position: absolute;
		width: 1px;
		height: 1px;
		padding: 0;
		margin: -1px;
		overflow: hidden;
		clip: rect(0, 0, 0, 0);
		white-space: nowrap;
		border-width: 0
		}

		.pointer-events-none{
		pointer-events: none
		}

		.absolute{
		position: absolute
		}

		.relative{
		position: relative
		}

		.inset-y-0{
		top: 0px;
		bottom: 0px
		}

		.left-0{
		left: 0px
		}

		.mb-2{
		margin-bottom: 0.5rem
		}

		.block{
		display: block
		}

		.flex{
		display: flex
		}

		.table{
		display: table
		}

		.h-4{
		height: 1rem
		}

		.h-5{
		height: 1.25rem
		}

		.w-4{
		width: 1rem
		}

		.w-5{
		width: 1.25rem
		}

		.w-80{
		width: 20rem
		}

		.w-full{
		width: 100%
		}

		.items-center{
		align-items: center
		}

		.justify-between{
		justify-content: space-between
		}

		.overflow-x-auto{
		overflow-x: auto
		}

		.rounded{
		border-radius: 0.25rem
		}

		.rounded-lg{
		border-radius: 0.5rem
		}

		.border{
		border-width: 1px
		}

		.border-b{
		border-bottom-width: 1px
		}

		.border-gray-300{
		--tw-border-opacity: 1;
		border-color: rgb(209 213 219 / var(--tw-border-opacity))
		}

		.bg-gray-100{
		--tw-bg-opacity: 1;
		background-color: rgb(243 244 246 / var(--tw-bg-opacity))
		}

		.bg-gray-50{
		--tw-bg-opacity: 1;
		background-color: rgb(249 250 251 / var(--tw-bg-opacity))
		}

		.bg-white{
		--tw-bg-opacity: 1;
		background-color: rgb(255 255 255 / var(--tw-bg-opacity))
		}

		.p-2{
		padding: 0.5rem
		}

		.px-3{
		padding-left: 0.75rem;
		padding-right: 0.75rem
		}

		.px-6{
		padding-left: 1.5rem;
		padding-right: 1.5rem
		}

		.py-1{
		padding-top: 0.25rem;
		padding-bottom: 0.25rem
		}

		.py-2{
		padding-top: 0.5rem;
		padding-bottom: 0.5rem
		}

		.py-3{
		padding-top: 0.75rem;
		padding-bottom: 0.75rem
		}

		.pr-2{
		padding-right: 0.5rem
		}

		.ps-10{
		padding-inline-start: 2.5rem !important
		}

		.ps-3{
		padding-inline-start: 0.75rem
		}

		.text-left{
		text-align: left
		}

		.text-lg{
		font-size: 1.125rem;
		line-height: 1.75rem
		}

		.text-sm{
		font-size: 0.875rem;
		line-height: 1.25rem
		}

		.text-xl{
		font-size: 1.25rem;
		line-height: 1.75rem
		}

		.text-xs{
		font-size: 0.75rem;
		line-height: 1rem
		}

		.font-semibold{
		font-weight: 600
		}

		.uppercase{
		text-transform: uppercase
		}

		.text-blue-600{
		--tw-text-opacity: 1;
		color: rgb(37 99 235 / var(--tw-text-opacity))
		}

		.text-gray-500{
		--tw-text-opacity: 1;
		color: rgb(107 114 128 / var(--tw-text-opacity))
		}

		.text-gray-700{
		--tw-text-opacity: 1;
		color: rgb(55 65 81 / var(--tw-text-opacity))
		}

		.text-gray-900{
		--tw-text-opacity: 1;
		color: rgb(17 24 39 / var(--tw-text-opacity))
		}

		.filter{
		filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
		}

		.focus\:border-blue-500:focus{
		--tw-border-opacity: 1;
		border-color: rgb(59 130 246 / var(--tw-border-opacity))
		}

		.focus\:ring-blue-500:focus{
		--tw-ring-opacity: 1;
		--tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity))
		}

		@media (min-width: 640px){
		.sm\:rounded-sm{
			border-radius: 0.25rem
		}
		}

		.rtl\:right-0:where([dir="rtl"], [dir="rtl"] *){
		right: 0px
		}

		.rtl\:text-right:where([dir="rtl"], [dir="rtl"] *){
		text-align: right
		}


	</style>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				let selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});

			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});

			$('.from-check-all').click(function () {
				checkAll(this);
			});
		});
	</script>
	{{-- include custom script for listings table [select all checkbox]  --}}
	<script>
		function checkAll(bx) {
			if (bx.type !== 'checkbox') {
				bx = document.getElementById('checkAll');
				bx.checked = !bx.checked;
			}
			
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type === 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection