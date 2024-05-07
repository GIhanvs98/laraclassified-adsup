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

@php
	$addListingUrl = (isset($addListingUrl)) ? $addListingUrl : \App\Helpers\UrlGen::addPost();
	$addListingAttr = '';
	if (!auth()->check()) {
		if (config('settings.single.guests_can_post_listings') != '1') {
			$addListingUrl = '#quickLogin';
			$addListingAttr = ' data-bs-toggle="modal"';
		}
	}
@endphp

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container inner-page">
		<div class="container" id="pricing" style="background: white;padding-top:20px;padding-bottom:20px;">

      @php
        if(isset($category)){
          $category = $category;
        }else{
          $category = "memberships";
        }
      @endphp

      @include("pages.inc.".$section, ['message' => $message, 'category' => $category ])

		</div>
	</div>
@endsection

@section('after_styles')
<style>
.mx-auto{
  margin-left: auto;
  margin-right: auto
}

.my-8{
  margin-top: 2rem;
  margin-bottom: 2rem
}

.mb-2{
  margin-bottom: 0.5rem
}

.mb-6{
  margin-bottom: 1.5rem
}

.mb-8{
  margin-bottom: 2rem
}

.mr-2{
  margin-right: 0.5rem
}

.mt-4{
  margin-top: 1rem
}

.flex{
  display: flex
}

.h-20{
  height: 5rem
}

.h-6{
  height: 1.5rem
}

.h-full{
  height: 100%
}

.w-6{
  width: 1.5rem
}

.w-full{
  width: 100%
}

.max-w-md{
  max-width: 28rem
}

.flex-col{
  flex-direction: column
}

.items-center{
  align-items: center
}

.justify-center{
  justify-content: center
}

.rounded-full{
  border-radius: 9999px
}

.rounded-lg{
  border-radius: 0.5rem
}

.bg-blue-700{
  --tw-bg-opacity: 1;
  background-color: rgb(29 78 216 / var(--tw-bg-opacity))
}

.bg-green-700{
  --tw-bg-opacity: 1;
  background-color: rgb(21 128 61 / var(--tw-bg-opacity))
}

.bg-orange-500{
  --tw-bg-opacity: 1;
  background-color: rgb(249 115 22 / var(--tw-bg-opacity))
}

.bg-red-700{
  --tw-bg-opacity: 1;
  background-color: rgb(185 28 28 / var(--tw-bg-opacity))
}

.p-16{
  padding: 4rem
}

.px-5{
  padding-left: 1.25rem;
  padding-right: 1.25rem
}

.py-2{
  padding-top: 0.5rem;
  padding-bottom: 0.5rem
}

.py-2\.5{
  padding-top: 0.625rem;
  padding-bottom: 0.625rem
}

.text-center{
  text-align: center
}

.text-2xl{
  font-size: 1.5rem;
  line-height: 2rem
}

.text-sm{
  font-size: 0.875rem;
  line-height: 1.25rem
}

.font-medium{
  font-weight: 500
}

.font-semibold{
  font-weight: 600
}

.text-blue-700{
  --tw-text-opacity: 1;
  color: rgb(29 78 216 / var(--tw-text-opacity))
}

.text-gray-700{
  --tw-text-opacity: 1;
  color: rgb(55 65 81 / var(--tw-text-opacity))
}

.text-green-600{
  --tw-text-opacity: 1;
  color: rgb(22 163 74 / var(--tw-text-opacity))
}

.text-orange-600{
  --tw-text-opacity: 1;
  color: rgb(234 88 12 / var(--tw-text-opacity))
}

.text-red-600{
  --tw-text-opacity: 1;
  color: rgb(220 38 38 / var(--tw-text-opacity))
}

.text-white{
  --tw-text-opacity: 1;
  color: rgb(255 255 255 / var(--tw-text-opacity))
}

.hover\:bg-blue-800:hover{
  --tw-bg-opacity: 1;
  background-color: rgb(30 64 175 / var(--tw-bg-opacity))
}

.hover\:bg-green-800:hover{
  --tw-bg-opacity: 1;
  background-color: rgb(22 101 52 / var(--tw-bg-opacity))
}

.hover\:bg-orange-500:hover{
  --tw-bg-opacity: 1;
  background-color: rgb(249 115 22 / var(--tw-bg-opacity))
}

.hover\:bg-red-800:hover{
  --tw-bg-opacity: 1;
  background-color: rgb(153 27 27 / var(--tw-bg-opacity))
}

.focus\:outline-none:focus{
  outline: 2px solid transparent;
  outline-offset: 2px
}

.focus\:ring-4:focus{
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
}

.focus\:ring-blue-300:focus{
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(147 197 253 / var(--tw-ring-opacity))
}

.focus\:ring-green-300:focus{
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(134 239 172 / var(--tw-ring-opacity))
}

.focus\:ring-orange-300:focus{
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(253 186 116 / var(--tw-ring-opacity))
}

.focus\:ring-red-300:focus{
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(252 165 165 / var(--tw-ring-opacity))
}

@media (min-width: 768px){
  .md\:text-3xl{
    font-size: 1.875rem;
    line-height: 2.25rem
  }
}

</style>
@endsection

@section('after_scripts')
@endsection
