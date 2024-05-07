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

<div class="main-container">
    <div class="container">
        <div class="row">

            @includeFirst([config('larapen.core.customizedViewPath') . 'post.inc.notification', 'post.inc.notification'])

            <div class="col-md-12 page-content max-w-screen-lg">
                <div class="inner-box category-content bg-white" style="overflow: visible;">

                    <div id="post-limit-warning" style="display: none;">

                        <!-- To Sell something or Looking for Buy/Rent: -->
                        <h2 class="title-2 text-center text-gray-700 text-2xl border-0 mt-[30px] font-light">
                            <strong>Dear {{ auth()->user()->name }}, You have exceeded your post limit for <span class="category-name" style="font-weight: inherit;"></span> category.</strong>
                        </h2>
                        <p class="mb-[30px] w-full text-center">Please upgrade your membership.</p>

                    </div>

                    <div id="post">

                        <div id="main-category-selection">

                            <!-- To Sell something or Looking for Buy/Rent: -->
                            <h2 class="title-2 text-center text-gray-700  text-2xl border-0 mt-[40px] font-light">
                                <strong>Dear {{ auth()->user()->name }}, Please select the correct option below to post your ad</strong>
                            </h2>

                            <div class="h-fit bg grid sm:grid-cols-3 grid-cols-1">

                                @forelse($categoryGroups as $categoryGroup)
                                <div class="w-full">
                                    <div class="m-4 p-6 border border-gray-200 border-solid rounded-sm">
                                        <h3 class="w-full flex items-center justify-center text-xl">
                                            {!! $categoryGroup->icon !!}
                                            <span>{{ $categoryGroup->name }}</span>
                                        </h3>

                                        @forelse($categoryGroup->subCategoryGroups as $subCategoryGroup)

                                        <p class="hover:cursor-pointer my-2 py-2 flex justify-between text-blue-600 h-full sub_category_group_bt" href="#browseCategories" data-bs-toggle="modal" sub_category_group_id="{{ $subCategoryGroup->id }}">
                                            <span>{{ $subCategoryGroup->name }}</span>
                                            <svg class="w-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                                            </svg>
                                        </p>
                                        @empty
                                        <p class="hover:cursor-pointer my-2 py-2 flex justify-between text-blue-600 h-full">
                                            <span>No sub category.</span>
                                        </p>
                                        @endforelse
                                    </div>
                                </div>
                                @empty
                                <div class="w-full text-center sm:col-span-3 col-span-1">
                                    No category list.
                                </div>
                                @endforelse

                            </div>

                            {{-- Button --}}
                            <!--div class="grid-cols-1">
									<div class="col-md-12 text-center">
										<button id="nextStepBtnToPostDetails" class="btn btn-primary btn-lg">{{ t('Next') }}</button>
									</div>
								</div-->

                            <div class="flex w-full">
                                @includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.right-sidebar', 'post.createOrEdit.inc.right-sidebar'])
                            </div>


                        </div>

                        <div id="location-selection">

                            <!-- 
								Select a City or Division:
								Districts
								-----------------
								Ampara
								Anuradhapura
								Badulla
								Batticaloa
								Colombo
								Galle
								Gampaha
								Hambantota
								Jaffna
								Kalutara
								Kandy
								Kegalle
								Kilinochchi
								Kurunegala
								Mannar
								Matale
								Matara
								Moneragala
								Mullativu
								Nuwara Eliya
								Polonnaruwa
								Puttalam
								Ratnapura
								Trincomalee
								Vavuniya

								Select a local area within Ampara:
								Popular Areas
								-----------------
								Akkarepattu
								Ampara
								Kalmunai
								Padiyatalawa
								Pothuvil
								Sainthamaruthu
								Samanthurai
								Siripura
								Uhana
								-->

                        </div>

                        <div id="post-details-section">

                            <!-- start normal data entry -->

                            <h2 class="title-2 text-center text-gray-700  text-2xl border-0 mt-[40px] font-light">
                                <strong>{{ t('create_new_listing') }}</strong>
                            </h2>

                            <div class="row">
                                <div class="col-xl-12">

                                    <form class="form-horizontal" id="postForm" method="POST" action="{{ request()->fullUrl() }}" enctype="multipart/form-data">
                                        {!! csrf_field() !!}
                                        <fieldset>

                                            {{-- category_id --}}
                                            <?php $categoryIdError = (isset($errors) && $errors->has('category_id')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required" style="display:none;">
                                                <label class="col-md-3 col-form-label{{ $categoryIdError }}">{{ t('category') }} <sup>*</sup></label>
                                                <div class="col-md-8">
                                                    <div id="catsContainer" class="rounded{{ $categoryIdError }}">
                                                        <a href="#browseCategories" data-bs-toggle="modal" class="cat-link" data-id="0">
                                                            {{ t('select_a_category') }}
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="category_id" id="categoryId" value="{{ old('category_id', data_get($postInput, 'category_id', 0)) }}">
                                                <input type="hidden" name="category_type" id="categoryType" value="{{ old('category_type', data_get($postInput, 'category_type')) }}">
                                            </div>

                                            @if (config('settings.single.show_listing_types'))
                                            {{-- post_type_id --}}
                                            @php
                                            $postTypeIdError = (isset($errors) && $errors->has('post_type_id')) ? ' is-invalid' : '';
                                            $postTypeId = old('post_type_id', data_get($postInput, 'post_type_id'));
                                            @endphp
                                            <div id="postTypeBloc" class="row mb-3 required">
                                                <label class="col-md-3 col-form-label">{{ t('type') }} <sup>*</sup></label>
                                                <div class="col-md-8">
                                                    @foreach ($postTypes as $postType)
                                                    <div class="form-check form-check-inline pt-2">
                                                        <input name="post_type_id" id="postTypeId-{{ data_get($postType, 'id') }}" value="{{ data_get($postType, 'id') }}" type="radio" class="form-check-input{{ $postTypeIdError }}" @checked($postTypeId==data_get($postType, 'id' ))>
                                                        <label class="form-check-label mb-0" for="postTypeId-{{ data_get($postType, 'id') }}">
                                                            {{ data_get($postType, 'name') }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                    <div class="form-text text-muted">{{ t('post_type_hint') }}</div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- title --}}
                                            <?php $titleError = (isset($errors) && $errors->has('title')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $titleError }}" for="title">{{ t('title') }} <sup>*</sup></label>
                                                <div class="col-md-8">
                                                    <input id="title" name="title" placeholder="{{ t('listing_title') }}" class="form-control input-md{{ $titleError }}" type="text" value="{{ old('title', data_get($postInput, 'title')) }}">
                                                    <div class="form-text text-muted">{{ t('a_great_title_needs_at_least_60_characters') }}</div>
                                                </div>
                                            </div>

                                            {{-- description --}}
                                            @php
                                            $descriptionError = (isset($errors) && $errors->has('description')) ? ' is-invalid' : '';
                                            $postDescription = data_get($postInput, 'description');
                                            $descriptionErrorLabel = '';
                                            $descriptionColClass = 'col-md-8';
                                            if (config('settings.single.wysiwyg_editor') != 'none') {
                                            $descriptionColClass = 'col-md-12';
                                            $descriptionErrorLabel = $descriptionError;
                                            }
                                            @endphp
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $descriptionErrorLabel }}" for="description">
                                                    {{ t('Description') }} <sup>*</sup>
                                                </label>
                                                <div class="{{ $descriptionColClass }}">
                                                    <textarea class="form-control{{ $descriptionError }}" id="description" name="description" rows="15" style="height: 300px">{{ old('description', $postDescription) }}</textarea>
                                                    <div class="form-text text-muted">{{ t('describe_what_makes_your_listing_unique') }}...</div>
                                                </div>
                                            </div>

                                            {{-- cfContainer --}}
                                            <div id="cfContainer"></div>

                                            {{-- price --}}
                                            @php
                                            $priceError = (isset($errors) && $errors->has('price')) ? ' is-invalid' : '';
                                            $currencySymbol = config('currency.symbol', 'X');
                                            $price = old('price', data_get($postInput, 'price'));
                                            $price = \App\Helpers\Number::format($price, 2, '.', '');
                                            @endphp
                                            <div id="priceBloc" class="row mb-3">
                                                <label class="col-md-3 col-form-label{{ $priceError }}" for="price">{{ t('price') }}</label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <span class="input-group-text">{!! $currencySymbol !!}</span>
                                                        <input id="price" name="price" class="form-control{{ $priceError }}" placeholder="{{ t('ei_price') }}" type="number" min="0" step="{{ getInputNumberStep((int)config('currency.decimal_places', 2)) }}" value="{!! $price !!}">
                                                        <span class="input-group-text">
                                                            <input id="negotiable" name="negotiable" type="checkbox" value="1" @checked(old('negotiable', data_get($postInput, 'negotiable' ))=='1' )>&nbsp;
                                                            <small>{{ t('negotiable') }}</small>
                                                        </span>
                                                    </div>
                                                    @if (config('settings.single.price_mandatory') != '1')
                                                    <div class="form-text text-muted">{{ t('price_hint') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- country_code --}}
                                            @php
                                            $countryCodeError = (isset($errors) && $errors->has('country_code')) ? ' is-invalid' : '';
                                            $countryCodeValue = (!empty(config('ipCountry.code'))) ? config('ipCountry.code') : 0;
                                            $countryCodeValue = data_get($postInput, 'country_code', $countryCodeValue);
                                            $countryCodeValueOld = old('country_code', $countryCodeValue);
                                            @endphp
                                            @if (empty(config('country.code')))
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $countryCodeError }}" for="country_code">
                                                    {{ t('your_country') }} <sup>*</sup>
                                                </label>
                                                <div class="col-md-8">
                                                    <select id="countryCode" name="country_code" class="form-control large-data-selecter{{ $countryCodeError }}">
                                                        <option value="0" data-admin-type="0" @selected(empty(old('country_code')))>
                                                            {{ t('select_a_country') }}
                                                        </option>
                                                        @foreach ($countries as $item)
                                                        <option value="{{ data_get($item, 'code') }}" data-admin-type="{{ data_get($item, 'admin_type') }}" @selected($countryCodeValueOld==data_get($item, 'code' ))>
                                                            {{ data_get($item, 'name') }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @else
                                            <input id="countryCode" name="country_code" type="hidden" value="{{ config('country.code') }}">
                                            @endif

                                            @php
                                            $adminType = config('country.admin_type', 0);
                                            @endphp
                                            @if (config('settings.single.city_selection') == 'select')
                                            @if (in_array($adminType, ['1', '2']))
                                            {{-- admin_code --}}
                                            <?php $adminCodeError = (isset($errors) && $errors->has('admin_code')) ? ' is-invalid' : ''; ?>
                                            <div id="locationBox" class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $adminCodeError }}" for="admin_code">{{ t('location') }} <sup>*</sup></label>
                                                <div class="col-md-8">
                                                    <select id="adminCode" name="admin_code" class="form-control large-data-selecter{{ $adminCodeError }}">
                                                        <option value="0" @selected(empty(old('admin_code')))>
                                                            {{ t('select_your_location') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            @endif
                                            @else
                                            <input type="hidden" id="selectedAdminType" name="selected_admin_type" value="{{ old('selected_admin_type', $adminType) }}">
                                            <input type="hidden" id="selectedAdminCode" name="selected_admin_code" value="{{ old('selected_admin_code', 0) }}">
                                            <input type="hidden" id="selectedCityId" name="selected_city_id" value="{{ old('selected_city_id', 0) }}">
                                            <input type="hidden" id="selectedCityName" name="selected_city_name" value="{{ old('selected_city_name') }}">
                                            @endif

                                            {{-- city_id --}}
                                            <?php $cityIdError = (isset($errors) && $errors->has('city_id')) ? ' is-invalid' : ''; ?>
                                            <div id="cityBox" class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $cityIdError }}" for="city_id">{{ t('city') }} <sup>*</sup></label>
                                                <div class="col-md-8">
                                                    <select id="cityId" name="city_id" class="form-control large-data-selecter{{ $cityIdError }}">
                                                        <option value="0" @selected(empty(old('city_id')))>
                                                            {{ t('select_a_city') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- tags --}}
                                            @php
                                            $tagsError = (isset($errors) && $errors->has('tags.*')) ? ' is-invalid' : '';
                                            $tags = old('tags', data_get($postInput, 'tags'));
                                            @endphp
                                            {{--<div class="row mb-3">
													<label class="col-md-3 col-form-label{{ $tagsError }}" for="tags">{{ t('Tags') }}</label>
                                            <div class="col-md-8">
                                                <select id="tags" name="tags[]" class="form-control tags-selecter" multiple="multiple">
                                                    @if (!empty($tags))
                                                    @foreach($tags as $iTag)
                                                    <option selected="selected">{{ $iTag }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <div class="form-text text-muted">
                                                    {!! t('tags_hint', [
                                                    'limit' => (int)config('settings.single.tags_limit', 15),
                                                    'min' => (int)config('settings.single.tags_min_length', 2),
                                                    'max' => (int)config('settings.single.tags_max_length', 30)
                                                    ]) !!}
                                                </div>
                                            </div>
                                </div>--}}

                                {{-- is_permanent --}}
                                @if (config('settings.single.permanent_listings_enabled') == '3')
                                <input type="hidden" name="is_permanent" id="isPermanent" value="0">
                                @else
                                <?php $isPermanentError = (isset($errors) && $errors->has('is_permanent')) ? ' is-invalid' : ''; ?>
                                <div id="isPermanentBox" class="row mb-3 required hide">
                                    <label class="col-md-3 col-form-label"></label>
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input id="isPermanent" name="is_permanent" class="form-check-input mt-1{{ $isPermanentError }}" value="1" type="checkbox" @checked(old('is_permanent', data_get($postInput, 'is_permanent' ))=='1' )>
                                            <label class="form-check-label mt-0" for="is_permanent">
                                                {!! t('is_permanent_label') !!}
                                            </label>
                                        </div>
                                        <div class="form-text text-muted">{{ t('is_permanent_hint') }}</div>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
                                @endif


                                <div class="content-subheading">
                                    <i class="fas fa-user"></i>
                                    <strong>{{ t('seller_information') }}</strong>
                                </div>


                                {{-- contact_name --}}
                                <?php $contactNameError = (isset($errors) && $errors->has('contact_name')) ? ' is-invalid' : ''; ?>
                                @if (auth()->check())
                                <input id="contactName" name="contact_name" type="hidden" value="{{ auth()->user()->name }}">
                                @else
                                <div class="row mb-3 required">
                                    <label class="col-md-3 col-form-label{{ $contactNameError }}" for="contact_name">
                                        {{ t('your_name') }} <sup>*</sup>
                                    </label>
                                    <div class="col-md-9 col-lg-8 col-xl-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-user"></i></span>
                                            <input id="contactName" name="contact_name" placeholder="{{ t('your_name') }}" class="form-control input-md{{ $contactNameError }}" type="text" value="{{ old('contact_name', data_get($postInput, 'contact_name')) }}">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- auth_field (as notification channel) --}}
                                @php
                                $authFields = getAuthFields(true);
                                $authFieldError = (isset($errors) && $errors->has('auth_field')) ? ' is-invalid' : '';
                                $usersCanChooseNotifyChannel = isUsersCanChooseNotifyChannel();
                                $authFieldValue = ($usersCanChooseNotifyChannel) ? (old('auth_field', getAuthField())) : getAuthField();
                                @endphp
                                @if ($usersCanChooseNotifyChannel)
                                <div class="row mb-3 required">
                                    <label class="col-md-3 col-form-label" for="auth_field">{{ t('notifications_channel') }} <sup>*</sup></label>
                                    <div class="col-md-9">
                                        @foreach ($authFields as $iAuthField => $notificationType)
                                        <div class="form-check form-check-inline pt-2">
                                            <input name="auth_field" id="{{ $iAuthField }}AuthField" value="{{ $iAuthField }}" class="form-check-input auth-field-input{{ $authFieldError }}" type="radio" @checked($authFieldValue==$iAuthField)>
                                            <label class="form-check-label mb-0" for="{{ $iAuthField }}AuthField">
                                                {{ $notificationType }}
                                            </label>
                                        </div>
                                        @endforeach
                                        <div class="form-text text-muted">
                                            {{ t('notifications_channel_hint') }}
                                        </div>
                                    </div>
                                </div>
                                @else
                                <input id="{{ $authFieldValue }}AuthField" name="auth_field" type="hidden" value="{{ $authFieldValue }}">
                                @endif

                                @php
                                $forceToDisplay = isBothAuthFieldsCanBeDisplayed() ? ' force-to-display' : '';
                                @endphp

                                {{-- email --}}
                                @php
                                $emailError = (isset($errors) && $errors->has('email')) ? ' is-invalid' : '';
                                $emailValue = (auth()->check() && isset(auth()->user()->email))
                                ? auth()->user()->email
                                : data_get($postInput, 'email');
                                @endphp
                                <div class="row mb-3 auth-field-item required{{ $forceToDisplay }}">
                                    <label class="col-md-3 col-form-label{{ $emailError }}" for="email">{{ t('email') }}
                                        @if (getAuthField() == 'email')
                                        <sup>*</sup>
                                        @endif
                                    </label>
                                    <div class="col-md-9 col-lg-8 col-xl-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                            <input id="email" name="email" class="form-control{{ $emailError }}" placeholder="{{ t('email_address') }}" type="text" value="{{ old('email', $emailValue) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- phone --}}
                                @php
                                $phoneError = (isset($errors) && $errors->has('phone')) ? ' is-invalid' : '';
                                $phoneValue = data_get($postInput, 'phone');
                                $phoneCountryValue = data_get($postInput, 'phone_country', config('country.code'));
                                if (
                                auth()->check()
                                && isset(auth()->user()->country_code)
                                && isset(auth()->user()->phone)
                                && isset(auth()->user()->phone_country)
                                // && auth()->user()->country_code == config('country.code')
                                ) {
                                $phoneValue = auth()->user()->phone;
                                $phoneCountryValue = auth()->user()->phone_country;
                                }
                                $phoneValue = phoneE164($phoneValue, $phoneCountryValue);
                                $phoneValueOld = phoneE164(old('phone', $phoneValue), old('phone_country', $phoneCountryValue));
                                @endphp
                                <div class="row mb-3 auth-field-item required{{ $forceToDisplay }}">
                                    <label class="col-md-3 col-form-label{{ $phoneError }}" for="phone">{{ t('phone_number') }}
                                        @if (getAuthField() == 'phone')
                                        <sup>*</sup>
                                        @endif
                                    </label>
                                    <div class="col-md-9 col-lg-8 col-xl-6">
                                        <div class="input-group">
                                            <input id="phone" name="phone" class="form-control input-md{{ $phoneError }}" type="tel" value="{{ $phoneValueOld }}">
                                            <span class="input-group-text iti-group-text">
                                                <input id="phoneHidden" name="phone_hidden" type="checkbox" value="1" @checked(old('phone_hidden')=='1' )>&nbsp;
                                                <small>{{ t('Hide') }}</small>
                                            </span>
                                        </div>
                                        <input name="phone_country" type="hidden" value="{{ old('phone_country', $phoneCountryValue) }}">
                                    </div>
                                </div>

                                @if (!auth()->check())
                                @if (in_array(config('settings.single.auto_registration'), [1, 2]))
                                {{-- auto_registration --}}
                                @if (config('settings.single.auto_registration') == 1)
                                <?php $autoRegistrationError = (isset($errors) && $errors->has('auto_registration')) ? ' is-invalid' : ''; ?>
                                <div class="row mb-3 required">
                                    <label class="col-md-3 col-form-label"></label>
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input name="auto_registration" id="auto_registration" class="form-check-input{{ $autoRegistrationError }}" value="1" type="checkbox" checked="checked">
                                            <label class="form-check-label" for="auto_registration">
                                                {!! t('I want to register by submitting this listing') !!}
                                            </label>
                                        </div>
                                        <div class="form-text text-muted">{{ t('You will receive your authentication information by email') }}</div>
                                    </div>
                                </div>
                                @else
                                <input type="hidden" name="auto_registration" id="auto_registration" value="1">
                                @endif
                                @endif
                                @endif

                                @include('layouts.inc.tools.captcha', ['colLeft' => 'col-md-3', 'colRight' => 'col-md-8'])

                                @if (!auth()->check())
                                {{-- accept_terms --}}
                                @php
                                $acceptTermsError = (isset($errors) && $errors->has('accept_terms')) ? ' is-invalid' : '';
                                $acceptTerms = old('accept_terms', data_get($postInput, 'accept_terms'));
                                @endphp
                                <div class="row mb-3 required">
                                    <label class="col-md-3 col-form-label"></label>
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input name="accept_terms" id="acceptTerms" class="form-check-input{{ $acceptTermsError }}" value="1" type="checkbox" @checked($acceptTerms=='1' )>
                                            <label class="form-check-label" for="acceptTerms" style="font-weight: normal;">
                                                {!! t('accept_terms_label', ['attributes' => getUrlPageByType('terms')]) !!}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- accept_marketing_offers --}}
                                @php
                                $acceptMarketingOffersError = (isset($errors) && $errors->has('accept_marketing_offers')) ? ' is-invalid' : '';
                                $acceptMarketingOffers = old('accept_marketing_offers', data_get($postInput, 'accept_marketing_offers'));
                                @endphp
                                <div class="row mb-3 required">
                                    <label class="col-md-3 col-form-label"></label>
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input name="accept_marketing_offers" id="acceptMarketingOffers" class="form-check-input{{ $acceptMarketingOffersError }}" value="1" type="checkbox" @checked($acceptMarketingOffers=='1' )>
                                            <label class="form-check-label" for="acceptMarketingOffers" style="font-weight: normal;">
                                                {!! t('accept_marketing_offers_label') !!}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Button --}}
                                <div class="row mb-3 pt-3">
                                    <div class="col-md-12 text-center">
                                        <a id="prevStepBtnToCategories" class="btn btn-default btn-lg">{{ t('Previous') }}</a>
                                        <button id="nextStepBtn" class="btn btn-primary btn-lg">{{ t('Next') }}</button>
                                    </div>
                                </div>

                                </fieldset>
                                </form>

                            </div>
                        </div>

                        <!-- end normal data entry -->

                    </div>

                </div>

                <div id="post-error" style="display: none;">

                    <!-- To Sell something or Looking for Buy/Rent: -->
                    <h2 class="title-2 text-center text-gray-700 text-2xl border-0 mt-[30px] font-light">
                        <strong>Unknown error occured.</strong>
                    </h2>
                    <p class="mb-[30px] w-full text-center">Please try again.</p>

                </div>

                <div id="post-loading" style="display: none;">

                    <!-- To Sell something or Looking for Buy/Rent: -->
                    <h2 class="title-2 text-center text-gray-700 text-xl border-0 mt-[30px] font-light">
                        <strong>
                            <svg aria-hidden="true" role="status" class="mr-3 inline h-4 w-4 animate-spin text-gray-200 dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2" /></svg>
                            &nbsp;&nbsp;Processing...
                        </strong>
                    </h2>

                </div>

            </div>
        </div>
        <!-- /.page-content -->

    </div>
</div>
</div>
@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.category-modal', 'post.createOrEdit.inc.category-modal'])
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
<script>
    $(function() {

        $("#post-limit-warning").hide();
        $("#post-error").hide();
        $("#post-loading").hide();
        $("#post").show();


        $(document).on('click', '.cat-link[data-parent-id=null]', function(e) {
            $(".category-name").text("`" + $(this).text() + "`");
        });

        $(document).on('click', '.cat-link[data-has-children=0]', function(e) {
            e.preventDefault(); /* prevents submit or reload */

            $("#post-limit-warning").hide();
            $("#post-error").hide();
            $("#post-loading").show();
            $("#post").hide();

            $.post("{{ route('post.check-limit') }}", {
                category: $(this).attr("data-id")
            }).done(function(data) {

                $("#post-loading").hide();

                if (data == "post") {
                    $("#post-limit-warning").hide();
                    $("#post-error").hide();
                    $("#post").show();

                    $("#main-category-selection").hide();
                    $("#post-details-section").show();

                } else if (data == "post-limit-warning") {
                    $("#post-limit-warning").show();
                    $("#post-error").remove();
                    $("#post").remove();
                } else {
                    $("#post-limit-warning").remove();
                    $("#post-error").show();
                    $("#post").remove();
                }
            });
        });

    });

</script>
@endsection

@includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.form-assets', 'post.createOrEdit.inc.form-assets'])
