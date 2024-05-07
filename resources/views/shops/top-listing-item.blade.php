@forelse($topResults as $key => $topResult)

@php

$post = $topResult->post;

@endphp

<div class="item-list border border-orange-400 bg-orange-50 rounded shadow-sm hover:shadow-orange-400" onclick="window.location.href='{{ \App\Helpers\UrlGen::post($post) }}'">
    <div class="d-flex">
        <div class="no-padding photobox">
            <div class="add-image">
                <!--span class="photo-count">
                    <i class="fa fa-camera"></i> {{ data_get($post, 'count_pictures') }}
                </span-->
                @if(isset($post->pictures()->first()->thumbnailImage) && isset($post->pictures()->first()->thumbnailImage->filename) && !empty($post->pictures()->first()->thumbnailImage->filename))
                    <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . $post->pictures()->first()->thumbnailImage->filename) }}" alt="{{ data_get($post, 'title') }}">
                @else
                    <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
                @endif
            </div>
        </div>

        <div class="col add-desc-box">
            <div class="items-details">
                <h5 class="add-title" style="padding-left: 5px;">
                    <a href="{{ \App\Helpers\UrlGen::post($post) }}" style="color: #333;">{{ $post->title }}</a>
                </h5>

                @include('shops.special-fields', ['padding' => 1])

                <span class="info-row">
                    <span class="category-data">
                        <i class="bi bi-folder hidden"></i>
                        <span class="item-location" style="color: inherit; padding-left: 5px;">
                            <!-- <i class="bi bi-geo-alt"></i> -->
                            <span class="item-location" {!! (config('lang.direction')=='rtl' ) ? ' dir="rtl"' : '' !!} style="color: inherit;">
                                <!-- <i class="bi bi-geo-alt"></i> -->
                                <a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city'), null, $cat ?? null) !!}" class="info-link" style="color: #9d9d9d;">
                                    {{ data_get($post, 'city.name') }}
                                </a> {{ (!empty(data_get($post, 'distance'))) ? '- ' . round(data_get($post, 'distance'), 2) . getDistanceUnit() : '' }}
                            </span>,&nbsp;
                            <a href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category'), null, $city ?? null) !!}" class="info-link" style="color: #9d9d9d;">
                                {{ data_get($post, 'category.name') }}
                            </a>
                        </span>
                        @isset($post->price)
                            <h2 class="item-price">
                                {{ $post->currency->symbol}}.{{ number_format($post->price, 0, '', ',') }}
                                <small style="font-size: 12px;background-color: transparent !important;font-weight: 400;">
                                    @if(isset($post['price_unit']))
                                        {{ ucfirst(reverse_slug($post['price_unit'])) }}
                                    @endif
                                </small>
                            </h2>
                        @endisset
                    </span>
            </div>

            <span class="date" style="display: flex; justify-content: end; color: #9d9d9d; font-size: 12px; padding-top: 3px;"> 
                <div class="w-6 h-6">
                    @include('ad-promotion-icons.top-ad')
                </div>
            </span>
        </div>
    </div>
</div>

<style>
    .bg-orange-50 {
        --tw-bg-opacity: 1;
        background-color: rgb(255 247 237 / var(--tw-bg-opacity)) !important
    }

    .border-orange-400 {
        --tw-border-opacity: 1;
        border-color: rgb(251 146 60 / var(--tw-border-opacity)) !important
    }

    .shadow-sm {
        --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
        --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color) !important;
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow) !important
    }

    .hover\:shadow-orange-400:hover {
        --tw-shadow-color: #fb923c !important;
        --tw-shadow: var(--tw-shadow-colored) !important
    }
</style>

@empty
<!-- No Top Listing -->
@endforelse
