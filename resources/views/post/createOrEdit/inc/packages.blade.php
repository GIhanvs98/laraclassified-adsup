@if (isset($packages, $paymentMethods) && $packages->count() > 0 && $paymentMethods->count() > 0)

<div class="well pb-0">
    <!-- <h3><i class="fas fa-certificate icon-color-1"></i> {{ t('Premium Listing') }} </h3> -->
    <p class="px-6">
        {{ t('premium_plans_hint') }}
    </p>

    <div {{--class="md:grid grid-cols-2"--}}>
        <div class="py-4 px-6">
            <h3 class="text-xl font-bold truncate">Selected ad for promotion</h3>

            <div class="flex p-3 border border-solid border-gray-300">

                @if(isset($post->pictures()->first()->thumbnailImage) && isset($post->pictures()->first()->thumbnailImage->filename) && !empty($post->pictures()->first()->thumbnailImage->filename))
                    <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . $post->pictures()->first()->thumbnailImage->filename) }}" alt="{{ data_get($post, 'title') }}">
                @else
                    <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
                @endif

                <div class="ml-4">
                    <a class="text-lg font-medium text-blue-700">{{ $post->title }}</a>
                    <p class="text-sm text-gray-500">{{ data_get($post, 'city.name') }} , {{ data_get($post, 'category.name') }}</p>
                    @isset($post->price)
                        <p class="text-sm text-gray-500 mt-1">{{ $post->currency->symbol}}.{{ number_format($post->price, 0, '', ',') }} <small class="label bg-success" style="font-size: 12px;background-color: transparent !important;font-weight: 400;">{{ ucfirst(reverse_slug($post->price_unit)) }}</small></p>
                    @endisset
                </div>
            </div>

        </div>
        <div class="{{--border-0 md:border-l border-solid border-gray-300--}} py-4 px-6">
            <h3 class="text-xl font-bold">Make your ad stand out!</h3>
            <p class="text-sm text-gray-600 pb-2" style="border-width: 0px 0px 1px 0px;">Get up to 10x more responses on your ads by applying a promotion</p>

            <?php $packageIdError = (isset($errors) && $errors->has('package_id')) ? ' is-invalid' : ''; ?>

            @if(isset($topAds))
                <div class="border rounded-md border-gray-300 mt-3">
                    <div class="flex items-center bg-gray-100 py-2 px-4">
                        <img src="{{ asset('images/promotions/top-promo.png') }}" alt="Top ads" class="h-6">
                        <div class="ml-4">
                            <h4 class="text-sm font-medium p-0 m-0">Top ads</h4>
                            <p class="text-xs text-gray-600 p-0 m-0 mt-1">Get up to 10 times or more view by displaying your ad at the top!</p>
                        </div>
                    </div>

                    @foreach ($packages as $package)
                        @if($package['packge_type'] === "Top ads")

                            <label class="border-0 border-solid flex justify-between py-3 px-4 border-t hover:cursor-pointer font-medium text-sm m-0 {{ $packageIdError }} border-gray-300" for="packageId-{{ $package->id }}" style="border-top-width: 1px !important" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">
                                <div><input type="radio" class="form-check-input package-selection{{ $packageIdError }}" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}" data-name="{{ $package->name }}" data-currencysymbol="{{ $package->currency->symbol }}" data-currencyinleft="{{ $package->currency->in_left }}" {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }}></div>
                                {{--<div>{!! $package->name . $badge !!}</div>--}}
                                <div class="w-full pl-6">{{ (isset($package->promo_duration)) ? $package->promo_duration : "∞" }} days</div>
                                <div id="price-{{ $package->id }}" class="text-blue-700" style="white-space: nowrap;">
                                    @if ($package->currency->in_left == 1)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                    <span class="price-int">{{ $package->price }}</span>
                                    @if ($package->currency->in_left == 0)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                </div>
                            </label>

                        @endif
                    @endforeach
                </div>
            @endif

            @if(isset($bumpAds))
                <div class="border rounded-md border-gray-300 mt-3">
                    <div class="flex items-center bg-gray-100 py-2 px-4">
                        <img src="{{ asset('images/promotions/bump-promo.png') }}" alt="Bump Ads" class="h-6">
                        <div class="ml-4">
                            <h4 class="text-sm font-medium p-0 m-0">Bounce ads</h4>
                            <p class="text-xs text-gray-600 p-0 m-0 mt-1">Get a fresh start every day and gain upto 5 times or more views!</p>
                        </div>
                    </div>

                    @foreach ($packages as $package)
                        @if($package['packge_type'] === "Bump Ads")

                            <label class="border-0 border-solid flex justify-between py-3 px-4 border-t hover:cursor-pointer font-medium text-sm m-0 {{ $packageIdError }} border-gray-300" for="packageId-{{ $package->id }}" style="border-top-width: 1px !important" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">
                                <div><input type="radio" class="form-check-input package-selection{{ $packageIdError }}" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}" data-name="{{ $package->name }}" data-currencysymbol="{{ $package->currency->symbol }}" data-currencyinleft="{{ $package->currency->in_left }}" {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }}></div>
                                {{--<div>{!! $package->name . $badge !!}</div>--}}
                                <div class="w-full pl-6">{{ (isset($package->promo_duration)) ? $package->promo_duration : "∞" }} days</div>
                                <div id="price-{{ $package->id }}" class="text-blue-700" style="white-space: nowrap;">
                                    @if ($package->currency->in_left == 1)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                    <span class="price-int">{{ $package->price }}</span>
                                    @if ($package->currency->in_left == 0)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                </div>
                            </label>

                        @endif
                    @endforeach

                </div>
            @endif

            @foreach ($packages as $package)
                @if($package['packge_type'] === "Free Ads")

                    <label class="block border rounded-md border-gray-300 mt-3 hover:cursor-pointer m-0 {{ $packageIdError }}" for="packageId-{{ $package->id }}" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">
                        <div class="flex items-center bg-gray-100 py-2 px-4">
                            <input type="radio" class="form-check-input package-selection{{ $packageIdError }}" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}" data-name="{{ $package->name }}" data-currencysymbol="{{ $package->currency->symbol }}" data-currencyinleft="{{ $package->currency->in_left }}" {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }}>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium p-0 m-0">Free ads</h4>
                                <p class="text-xs text-gray-600 p-0 m-0 mt-1">Reach your customers with our free ad service!</p>
                                <div class="hidden w-full pl-6">{{ (isset($package->promo_duration)) ? $package->promo_duration : "∞" }} days</div>
                                <div id="price-{{ $package->id }}" class="hidden text-blue-700" style="white-space: nowrap;">
                                    @if ($package->currency->in_left == 1)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                    <span class="price-int">{{ $package->price }}</span>
                                    @if ($package->currency->in_left == 0)
                                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </label>
                @endif
            @endforeach

            <table id="packagesTable" class="table table-hover checkboxtable mb-0">
                {{--
					@if(isset($topAds))
						<tr>
							<td style="box-shadow: none; border-width: 0px;">
							<h3><i class="fas fa-certificate icon-color-1"></i> Top ads </h3>
							</td>
						</tr>
						
						@foreach ($packages as $package)
							@if($package['packge_type'] === "Top ads")
							@php
									$packageStatus = '';
									$badge = '';
									if (isset($currentPackageId, $currentPackagePrice, $currentPaymentIsActive)) {
										// Prevent Package's Downgrading
										if ($currentPackagePrice > $package->price) {
											$packageStatus = ' disabled';
											$badge = ' <span class="badge bg-danger">' . t('Not available') . '</span>';
										} elseif ($currentPackagePrice == $package->price) {
											$badge = '';
										} else {
											if ($package->price > 0) {
												$badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
											}
										}
										if ($currentPackageId == $package->id) {
											$badge = ' <span class="badge bg-secondary">' . t('Current') . '</span>';
											if ($currentPaymentIsActive == 0) {
												$badge .= ' <span class="badge bg-warning">' . t('Payment pending') . '</span>';
											}
										}
									} else {
										if ($package->price > 0) {
											$badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
										}
									}
								@endphp
								<tr>
									<td class="text-start align-middle p-3">
										<div class="form-check">
											<input class="form-check-input package-selection{{ $packageIdError }}"
                type="radio"
                name="package_id"
                id="packageId-{{ $package->id }}"
                value="{{ $package->id }}"
                data-name="{{ $package->name }}"
                data-currencysymbol="{{ $package->currency->symbol }}"
                data-currencyinleft="{{ $package->currency->in_left }}"
                {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{
													$packageStatus }}
                >
                <label class="form-check-label mb-0{{ $packageIdError }}">
                    <strong class="" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">{!! $package->name . $badge !!} </strong>
                </label>
        </div>
        </td>
        <td class="text-end align-middle p-3">
            <p id="price-{{ $package->id }}" class="mb-0">
                @if ($package->currency->in_left == 1)
                <span class="price-currency">{!! $package->currency->symbol !!}</span>
                @endif
                <span class="price-int">{{ $package->price }}</span>
                @if ($package->currency->in_left == 0)
                <span class="price-currency">{!! $package->currency->symbol !!}</span>
                @endif
            </p>
        </td>
        </tr>
        @endif
        @endforeach
        @endif

        @if(isset($bumpAds))
        <tr>
            <td style="box-shadow: none;">
                <h3><i class="fas fa-certificate icon-color-1"></i> Bump Ads </h3>
            </td>
        </tr>

        @foreach ($packages as $package)
        @if($package['packge_type'] === "Bump Ads")
        @php
        $packageStatus = '';
        $badge = '';
        if (isset($currentPackageId, $currentPackagePrice, $currentPaymentIsActive)) {
        // Prevent Package's Downgrading
        if ($currentPackagePrice > $package->price) {
        $packageStatus = ' disabled';
        $badge = ' <span class="badge bg-danger">' . t('Not available') . '</span>';
        } elseif ($currentPackagePrice == $package->price) {
        $badge = '';
        } else {
        if ($package->price > 0) {
        $badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
        }
        }
        if ($currentPackageId == $package->id) {
        $badge = ' <span class="badge bg-secondary">' . t('Current') . '</span>';
        if ($currentPaymentIsActive == 0) {
        $badge .= ' <span class="badge bg-warning">' . t('Payment pending') . '</span>';
        }
        }
        } else {
        if ($package->price > 0) {
        $badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
        }
        }
        @endphp
        <tr>
            <td class="text-start align-middle p-3">
                <div class="form-check">
                    <input class="form-check-input package-selection{{ $packageIdError }}" type="radio" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}" data-name="{{ $package->name }}" data-currencysymbol="{{ $package->currency->symbol }}" data-currencyinleft="{{ $package->currency->in_left }}" {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{
												$packageStatus }}>
                    <label class="form-check-label mb-0{{ $packageIdError }}">
                        <strong class="" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">{!! $package->name . $badge !!} </strong>
                    </label>
                </div>
            </td>
            <td class="text-end align-middle p-3">
                <p id="price-{{ $package->id }}" class="mb-0">
                    @if ($package->currency->in_left == 1)
                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                    @endif
                    <span class="price-int">{{ $package->price }}</span>
                    @if ($package->currency->in_left == 0)
                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                    @endif
                </p>
            </td>
        </tr>

        @endif
        @endforeach
        @endif

        @foreach ($packages as $package)
        @if($package['packge_type'] === "Free Ads")

        <tr>
            <td style="box-shadow: none;">
                <h3><i class="fas fa-certificate icon-color-1"></i> Free Ads </h3>
            </td>
        </tr>

        @php
        $packageStatus = '';
        $badge = '';
        if (isset($currentPackageId, $currentPackagePrice, $currentPaymentIsActive)) {
        // Prevent Package's Downgrading
        if ($currentPackagePrice > $package->price) {
        $packageStatus = ' disabled';
        $badge = ' <span class="badge bg-danger">' . t('Not available') . '</span>';
        } elseif ($currentPackagePrice == $package->price) {
        $badge = '';
        } else {
        if ($package->price > 0) {
        $badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
        }
        }
        if ($currentPackageId == $package->id) {
        $badge = ' <span class="badge bg-secondary">' . t('Current') . '</span>';
        if ($currentPaymentIsActive == 0) {
        $badge .= ' <span class="badge bg-warning">' . t('Payment pending') . '</span>';
        }
        }
        } else {
        if ($package->price > 0) {
        $badge = ' <span class="badge bg-success">' . t('Upgrade') . '</span>';
        }
        }
        @endphp
        <tr>
            <td class="text-start align-middle p-3">
                <div class="form-check">
                    <input class="form-check-input package-selection{{ $packageIdError }}" type="radio" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}" data-name="{{ $package->name }}" data-currencysymbol="{{ $package->currency->symbol }}" data-currencyinleft="{{ $package->currency->in_left }}" {{ (old('package_id', $currentPackageId ?? 0)==$package->id) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{
												$packageStatus }}>
                    <label class="form-check-label mb-0{{ $packageIdError }}">
                        <strong class="" data-bs-placement="right" data-bs-toggle="tooltip" title="{!! $package->description_string !!}">{!! $package->name . $badge !!} </strong>
                    </label>
                </div>
            </td>
            <td class="text-end align-middle p-3">
                <p id="price-{{ $package->id }}" class="mb-0">
                    @if ($package->currency->in_left == 1)
                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                    @endif
                    <span class="price-int">{{ $package->price }}</span>
                    @if ($package->currency->in_left == 0)
                    <span class="price-currency">{!! $package->currency->symbol !!}</span>
                    @endif
                </p>
            </td>
        </tr>

        @endif
        @endforeach
        --}}

        <tr>
            <td class="text-start align-middle p-3" style="border-width: 0px;">
                @includeFirst([
                config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.payment-methods',
                'post.createOrEdit.inc.payment-methods'
                ])
            </td>
            <td class="text-end align-middle p-3" style="border-width: 0px;">
                <p class="mb-0">
                    <strong>
                        {{ t('Payable Amount') }}:
                        <span class="price-currency amount-currency currency-in-left"></span>
                        <span class="payable-amount">0</span>
                        <span class="price-currency amount-currency currency-in-right"></span>
                    </strong>
                </p>
            </td>
        </tr>
    </table>


    </div>
</div>

    <!--div class="row mb-3 mb-0">
        <div class="col-8"></div>
        <div class="col-4">
            <table>...</table>
        </div>
    </div-->

</div>

@includeFirst([
config('larapen.core.customizedViewPath') . 'post.createOrEdit.inc.payment-methods.plugins',
'post.createOrEdit.inc.payment-methods.plugins'
])

@endif
