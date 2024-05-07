<div class="item-list" onclick="window.location.href='{{ \App\Helpers\UrlGen::post($post) }}'">
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
                    {{ $post->title }}
                </h5>

                @include('shops.special-fields', ['padding' => 1])

                <span class="info-row">
                    <span class="category-data">
                        <i class="bi bi-folder hidden"></i>
                        <span class="item-location" style="color: inherit; padding-left: 5px;">
                            <!-- <i class="bi bi-geo-alt"></i> -->
                            <span class="item-location" {!! (config('lang.direction')=='rtl' ) ? ' dir="rtl"' : '' !!} style="color: inherit;">
                                <!-- <i class="bi bi-geo-alt"></i> -->
                                <a {{--href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city'), null, $cat ?? null) !!}"--}} class="info-link" style="color: #9d9d9d;">
                                    {{ data_get($post, 'city.name') }}
                                </a> {{ (!empty(data_get($post, 'distance'))) ? '- ' . round(data_get($post, 'distance'), 2) . getDistanceUnit() : '' }}
                            </span>,&nbsp;
                            <a {{--href="{!! \App\Helpers\UrlGen::category(data_get($post, 'category'), null, $city ?? null) !!}"--}} class="info-link" style="color: #9d9d9d;">
                                {{ data_get($post, 'category.name') }}
                            </a>
                        </span>
                        @isset($post->price)
                            <h2 class="item-price" style="padding-bottom: 0px;">
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

            <span class="date" style="display: flex; justify-content: end; color: #9d9d9d; font-size: 12px;">
                @if(isset($post->transactions))

                    @php

                        $bumpAd = $post->transactions()->where('payment_type', 'ad-promotion')
                            ->where('payment_status', 'success')
                            ->whereNotNull('payment_started_datetime')
                            ->whereNotNull('payment_valid_untill_datetime')
                            ->whereNotNull('payment_due_datetime')
                            ->where('active', 1)
                            ->where('payment_due_datetime', '>', now());

                        $bumpAd = $bumpAd->withWhereHas('package', function ($query) {

                        $query->where('packge_type', 'Bump Ads');
                            $query->where('active', 1);
                        })->first();

                    @endphp

                    @if($bumpAd)

                        <div class="w-6 h-6">
                            @include('ad-promotion-icons.bump-ad')
                        </div>

                    @else
                        {{ now()->longAbsoluteDiffForHumans($post->created_at) }}
                    @endif

                @else
                    {{ now()->longAbsoluteDiffForHumans($post->created_at) }}
                @endif

            </span>
        </div>
    </div>
</div>
