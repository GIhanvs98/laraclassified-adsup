<div class="px-2">

    <p class="text-xl font-medium">Promote your Ad</p>

    <p class="text-gray-400">The premium package help sellers to promote their products or services by giving more visibility to their listings to attract more buyers and sell faster.</p>

    <div class="mt-4 space-y-3 rounded-lg border bg-white px-0">
        <div class="flex flex-col rounded-lg bg-white sm:flex-row">

            <div class="m-2 rounded-md object-cover object-center">

                @if(\App\Models\Picture::where('post_id', $post->id)->exists())
                    <div style="background-image: url({{ asset(config('pictures.thumbnail_image.image_location') . '/' . $post->pictures()->first()->thumbnailImage->filename) }});" class="h-36 w-36 bg-cover bg-center bg-no-repeat rounded-lg m-2 flex items-center justify-center"></div>
                @else
                    <div style="background-image: url({{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }});" class="h-36 w-36 bg-cover bg-center bg-no-repeat rounded-lg m-2 flex items-center justify-center"></div>
                @endif
            </div>
            <div class="flex w-full flex-col px-4 py-1 justify-center">
                <a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ data_get($post, 'title') }}" class="text-lg font-semibold">
                    {{ $post->title }}
                </a>

                @include('shops.special-fields', ['padding' => 0])

                <p class="float-right text-gray-400 line-clamp-3">{{ $post->city->name }} , {{ $post->category->name }}</p>
                @isset($post->price)
                    <p class="text-base font-bold">{{ $post->currency->symbol}}.{{ number_format($post->price, 0, '', ',') }} <small class="label bg-success" style="font-size: 12px;background-color: transparent !important;font-weight: 400;">{{ ucfirst(reverse_slug($post->price_unit)) }}</small></p>
                @endisset
            </div>

        </div>
    </div>

    <form class="pt-4">

        <h3 class="text-xl font-bold">Make your ad stand out!</h3>

        <p class="text-sm text-gray-600 pb-2">Get up to 10x more responses on your ads by applying a promotion</p>


        <div wire:ignore class="accordion accordion-flush" id="promotionAccordition">

            @if($giveawayPackages && $totallyAllowedPackagesCount > $totallyUsedPackageCount)
                <div class="border rounded-md border-gray-300 mt-3">
                    <div class="accordion-button flex items-center bg-gray-100 py-3 px-4 cursor-pointer" id="heading-giveawayMembershipPackages" data-bs-toggle="collapse" data-bs-target="#body-giveawayMembershipPackages" aria-expanded="true" aria-controls="body-giveawayMembershipPackages">
                        {{--<img src="{{ asset('images/promotions/top-promo.png') }}" alt="Top ads" class="h-6">--}}
                        <div class="w-6 h-6">
                            @include('ad-promotion-icons.giveaway-membership-packages')
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium p-0 m-0">Membership Packages</h4>
                            <p class="text-xs text-gray-600 p-0 m-0 mt-1">Use these free ad promotions comes with your membership!</p>
                        </div>
                    </div>

                    <div id="body-giveawayMembershipPackages" class="collapse show" aria-labelledby="heading-giveawayMembershipPackages" data-bs-parent="#promotionAccordition">
                        @foreach ($giveawayPackages as $key => $package)
                            @if($package)

                                @php 
                                
                                $packageId = $package['id'];

                                $packageCount = $package['count'];

                                $usedPackageCount = \App\Models\Transaction::valid('ad-promotion')->where('user_id', auth()->user()->id)->where('package_id', $packageId)->giveawayTransactions()->count();

                                $getawayPackages = \App\Models\Package::find($package['id']); 
                                
                                @endphp

                                @if($packageCount > $usedPackageCount)
                                    <label wire:key="{{ $getawayPackages->id }}_{{ $key }}" class="border-0 border-solid flex justify-between py-3 px-4 border-t hover:cursor-pointer font-medium text-sm m-0 border-gray-300" for="packageId-{{ $getawayPackages->id }}_{{ $key }}" style="border-top-width: 1px !important" title="{!! $getawayPackages->description !!}">
                                        <div><input type="radio" wire:click="selectGiveawayMembershipPackage({{ $getawayPackages->id }})" class="form-check-input package-selection" name="package_id" id="packageId-{{ $getawayPackages->id }}_{{ $key }}" value="{{ $getawayPackages->id }}"></div>
                                        <div class="w-full pl-6">
                                            {{ $getawayPackages->name }} - {{ (isset($getawayPackages->promo_duration)) ? $getawayPackages->promo_duration : "∞" }} days
                                        </div>
                                        <div id="price-{{ $getawayPackages->id }}_{{ $key }}" class="text-blue-700" style="white-space: nowrap;">{{ $packageCount - $usedPackageCount }}X</div>
                                    </label>
                                @endif

                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($topAds))
                <div class="border rounded-md border-gray-300 mt-3">
                    <div class="accordion-button flex items-center bg-gray-100 py-3 px-4 collapsed cursor-pointer" id="heading-topAds" data-bs-toggle="collapse" data-bs-target="#body-topAds" aria-expanded="false" aria-controls="body-topAds">
                        {{--<img src="{{ asset('images/promotions/top-promo.png') }}" alt="Top ads" class="h-6">--}}
                        <div class="w-6 h-6">
                            @include('ad-promotion-icons.top-ad')
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium p-0 m-0">Top ads</h4>
                            <p class="text-xs text-gray-600 p-0 m-0 mt-1">Get up to 10 times or more view by displaying your ad at the top!</p>
                        </div>
                    </div>

                    <div id="body-topAds" class="collapse" aria-labelledby="heading-topAds" data-bs-parent="#promotionAccordition">
                        @foreach ($packages as $package)
                            @if($package->packge_type === "Top ads")
                                <label wire:key="{{ $package->id }}" class="border-0 border-solid flex justify-between py-3 px-4 border-t hover:cursor-pointer font-medium text-sm m-0 border-gray-300" for="packageId-{{ $package->id }}" style="border-top-width: 1px !important" title="{!! $package->description_string !!}">
                                    <div><input type="radio" wire:click="selectPackage({{ $package->id }})" class="form-check-input package-selection" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}"></div>
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
                </div>
            @endif

            @if(isset($bumpAds))
                <div class="border rounded-md border-gray-300 mt-3">
                    <div class="accordion-button flex items-center bg-gray-100 py-3 px-4 collapsed cursor-pointer" id="heading-bumpAds" data-bs-toggle="collapse" data-bs-target="#body-bumpAds" aria-expanded="false" aria-controls="body-bumpAds">
                        {{--<img src="{{ asset('images/promotions/bump-promo.png') }}" alt="Bump Ads" class="h-6">--}}
                        <div class="w-6 h-6">
                            @include('ad-promotion-icons.bump-ad')
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium p-0 m-0">Bounce ads</h4>
                            <p class="text-xs text-gray-600 p-0 m-0 mt-1">Get a fresh start every day and gain upto 5 times or more views!</p>
                        </div>
                    </div>

                    <div id="body-bumpAds" class="collapse" aria-labelledby="heading-bumpAds" data-bs-parent="#promotionAccordition">
                        @foreach ($packages as $package)
                            @if($package->packge_type === "Bump Ads")
                                <label wire:key="{{ $package->id }}" class="border-0 border-solid flex justify-between py-3 px-4 border-t hover:cursor-pointer font-medium text-sm m-0 border-gray-300" for="packageId-{{ $package->id }}" style="border-top-width: 1px !important" title="{!! $package->description_string !!}">
                                    <div><input type="radio" wire:click="selectPackage({{ $package->id }})" class="form-check-input package-selection" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}"></div>
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
                </div>
            @endif

            @foreach ($packages as $package)
                @if($package->packge_type === "Free Ads")
                    <label wire:key="{{ $package->id }}" class="block border rounded-md border-gray-300 mt-3 hover:cursor-pointer m-0" for="packageId-{{ $package->id }}" title="{!! $package->description_string !!}">
                        <div class="flex items-center bg-gray-100 py-3 px-4">
                            <input type="radio" wire:click="selectPackage({{ $package->id }})" class="form-check-input package-selection" name="package_id" id="packageId-{{ $package->id }}" value="{{ $package->id }}">
                            <div class="ml-4">
                                <h4 class="text-sm font-medium p-0 m-0">Free ads</h4>
                                <p class="text-xs text-gray-600 p-0 m-0 mt-1">Reach your customers with our free ad service (for {{$package->duration}} days)!</p>
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

        </div>
  
        <div class="row">
            <div class="col-md-12 text-center mt-4">

                <button type="reset" class="btn btn-default btn-lg mr-2">Clear</button>

                <button type="button" class="btn btn-success btn-lg" wire:loading.remove wire:click="save">Next</button>
                
                <button class="btn btn-primary btn-lg" wire:loading.inline-block wire:target="save">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                    </svg>
                    Loading...
                </button>

            </div>
        </div>

        @empty(!$error_output)
            <div style="margin-top: 20px;" class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>

                <span class="sr-only">Info</span>

                <div>{!! $error_output !!}</div>
            </div>
        @endempty

        <style>
            .collapse {
                visibility: inherit;
            }
            .accordion-button:not(.collapsed) {
                color: inherit;
                box-shadow: none;
            }
        </style>

        <script>
            $(document).ready(function() {
                // Prevent the accordion from closing when clicking on the radio button
                $('#accordionFlushExample input[type="radio"]').click(function(event) {
                    event.stopPropagation();
                });

                // Optional: If you also want to prevent closing when clicking anywhere inside the panel body
                $('#accordionFlushExample .card-body').click(function(event) {
                    event.stopPropagation();
                });
            });
        </script>

    </form>

</div>
