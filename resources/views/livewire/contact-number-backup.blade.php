@php
$post ??= [];
$user ??= [];
$countPackages ??= 0;
$countPaymentMethods ??= 0
@endphp
<aside style="position: sticky;top: 0px;" class="border-0 border-gray-200 border-t lg:border-t-0 border-solid">
    <div class="card card-user-info sidebar-card" style="border: 0px;border-radius: 0px;border-bottom: 1px solid rgba(0,0,0,.125);">

        <div class="block-cell user" style="border-radius: 0px 4px 0px 0px;">
            <div class="cell-media">
                @if (!empty($user) && isset($user->email))
                <a href="{{ route('shops.index', ['id'=> $user['id'], 'slug'=> \Illuminate\Support\Str::slug($user['name'], '-')]) }}">
                    <img src="{{ data_get($post, 'user_photo_url') }}" alt="{{ data_get($post, 'contact_name') }}">
                </a>
                @else
                <img src="{{ data_get($post, 'user_photo_url') }}" alt="{{ data_get($post, 'contact_name') }}">
                @endif

            </div>
            <div class="cell-content">

                <!-- Side bar output of user data starts. -->

                @php

                $userData = \App\Models\User::whereId($user['id'])->with('membership')->first();

                @endphp

                @if (!empty($userData) && isset($userData->email))
                {{-- If it's a registered user --}}

                @php

                // Validate the shop existance.
                $shopExists = \App\Models\Shop::where('user_id', $user['id'])->exists();

                // Validate membership not a free membership.
                $membershipExists = \App\Models\Membership::where('id', $userData->membership->id)->member()->exists();

                $nonMember = \App\Models\Membership::whereId(config('subscriptions.memberships.default_id'))->nonMember()->first();

                // Validate transactions. If the user has paid for the given time, then it is allowed. If he hasent payed for the current time.
                $transactionsValidExists = \App\Models\Transaction::valid('membership')
													->where('user_id', $userData->id)
													->exists();	

                @endphp

                @if($shopExists && $membershipExists && $transactionsValidExists)
                {{-- Paid membership with shop --}}

                <span class="name">
                    <a href="{{ route('shops.index', ['id'=> $user['id'], 'slug'=> \Illuminate\Support\Str::slug($user['name'], '-')]) }}">
                        {{ $user['name'] }}
                    </a>
                </span>

                <div class="member-badges mt-2">
                    <div class="flex w-fit items-center justify-start rounded-xl bg-gray-300 p-1">

                        @if(isset($userData->membership->icon) && $userData->membership->icon != "")

                        <div class="membership-icon h-fit w-5">
                            {!! $userData->membership->icon !!}
                        </div>

                        @endif

                        <div class="mt-[0.1rem] h-fit px-1 pl-2 text-xs font-semibold text-gray-900">{{ strtoupper($userData->membership->name) }}</div>

                    </div>
                </div>

                <div class="mt-1 text-sm" style="font-size: 10px;">User since {{ date_format(date_create($userData->created_at), "F Y") }}</div>

                <a href="{{ route('shops.index', ['id'=> $user['id'], 'slug'=> \Illuminate\Support\Str::slug($user['name'], '-')]) }}" class="mt-3 " style="font-size: 12px;">Visit member's shop</a>

                @else
                {{-- Payment behind membership or dont have a shop--}}

                <span class="name">{{ $user['name'] }}</span>

                <div class="member-badges mt-2">
                    <div class="flex w-fit items-center justify-start rounded-xl bg-gray-300 p-1">

                        @if(isset($nonMember->icon) && $nonMember->icon != "")

                        <div class="membership-icon h-fit w-5">
                            {!! $nonMember->icon !!}
                        </div>

                        @endif

                        <div class="mt-[0.1rem] h-fit px-1 pl-2 text-xs font-semibold text-gray-900">{{ strtoupper($nonMember->name) }}</div>

                    </div>
                </div>

                <div class="mt-1 text-sm" style="font-size: 10px;">User since {{ date_format(date_create($userData->created_at), "F Y") }}</div>

                @endif

                @else
                {{-- If user is a guest --}}

                <span class="name">{{ data_get($post, 'contact_name') }}</span>

                <div class="mt-1 text-sm" style="font-size: 10px;">Guest user since {{ date_format(date_create($userData->created_at), "F Y") }}</div>
                @endif

                {{--
                    @if (config('plugins.reviews.installed'))
                        @if (view()->exists('reviews::ratings-user'))
                            @include('reviews::ratings-user')
                        @endif
                    @endif
                --}}

                <!-- Side bar output of user data ends. -->

            </div>
        </div>

        <div class="card-content">
            @php
            $evActionStyle = 'style="border-top: 0;"';
            @endphp
            @if (!auth()->check() || (auth()->check() && auth()->user()->getAuthIdentifier() != data_get($post, 'user_id')))
            {{--<div class="card-body text-start">
					<div class="grid-col">
						<div class="col from">
							<i class="bi bi-geo-alt"></i>
							<span>{{ t('location') }}</span>
        </div>
        <div class="col to">
            <span>
                <a href="{!! \App\Helpers\UrlGen::city(data_get($post, 'city')) !!}">
                    {{ data_get($post, 'city.name') }}
                </a>
            </span>
        </div>
    </div>
    @if (!config('settings.single.hide_dates'))
    @if (!empty($user) && !empty(data_get($user, 'created_at_formatted')))
    <div class="grid-col">
        <div class="col from">
            <i class="bi bi-person-check"></i>
            <span>{{ t('Joined') }}</span>
        </div>
        <div class="col to">
            <span>{!! data_get($user, 'created_at_formatted') !!}</span>
        </div>
    </div>
    @endif
    @endif
    </div>
    @php
    $evActionStyle = 'style="border-top: 1px solid #ddd;"';
    @endphp--}}
    @endif

    <div class="ev-action" {!! $evActionStyle !!}>

        @php
        $user_get = DB::table('users')->find($user['id']);
        @endphp

        <div class="px-4 py-6" style="padding-top: 0px;padding-left: 10px !important;padding-right: 0px !important;">
            <livewire:contact-numbers :data-id="$post['id']" type="post" />
        </div>

        <div class="flex items-center justify-start px-4 py-6" style="padding: 0px !important;padding-left: 10px !important;">
            {{--
				<div class="w-6 fill-green-700">
					<svg viewBox="0 0 24 24" class="svg-wrapper--8ky9e">
						<path d="M24 3.79l-9 10.05a4.22 4.22 0 0 1-3 1.64 4.36 4.36 0 0 1-3-1.7L0 3.79v13.68a8.94 8.94 0 0 0 .1 1.27l5.23-5.37 1.34 1.35-5.34 5.37a10.11 10.11 0 0 0 1.34.12H22a1.69 1.69 0 0 0 .53-.12l-5.2-5.37 1.34-1.35 5.15 5.37a5.52 5.52 0 0 0 .18-1.52V3.79zm-11.24 9.27l8.57-9.27H2.67l8.57 9.27a1.26 1.26 0 0 0 .76.44 1.26 1.26 0 0 0 .76-.44z" fill-rule="evenodd"></path>
					</svg>
				</div>

				<div class="ml-4">
					<a href="mailto:{{ $user_get->email }}" target="_blank">
            <div>Send Email</div>
            </a>
        </div>
        --}}

        {!! genEmailContactBtn($post, true) !!}

    </div>

    </div>
    </div>
    </div>

    @if (config('settings.single.show_listing_on_googlemap'))
    @php
    $mapHeight = 250;
    $mapPlace = (!empty(data_get($post, 'city')))
    ? data_get($post, 'city.name') . ',' . config('country.name')
    : config('country.name');
    $mapUrl = getGoogleMapsEmbedUrl(config('services.googlemaps.key'), $mapPlace);
    @endphp
    <div class="card sidebar-card">
        <div class="card-header">{{ t('location_map') }}</div>
        <div class="card-content">
            <div class="card-body text-start p-0">
                <div class="posts-googlemaps">
                    <iframe id="googleMaps" width="100%" height="{{ $mapHeight }}" src="{{ $mapUrl }}"></iframe>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (isVerifiedPost($post))
    @includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.social.horizontal', 'layouts.inc.social.horizontal'])
    @endif

    <div class="card sidebar-card" style="border-radius: 0px 0px 4px 0px;border-width: 1px 0px 0px 0px;">
        <div class="card-header">{{ t('Safety Tips for Buyers') }}</div>
        <div class="card-content">
            <div class="card-body text-start">
                <ul class="list-check">
                    <li>{{ t('Meet seller at a public place') }}</li>
                    <li>{{ t('Check the item before you buy') }}</li>
                    <li>{{ t('Pay only after collecting the item') }}</li>
                </ul>
                @php
                $tipsLinkAttributes = getUrlPageByType('tips');
                @endphp
                @if (!str_contains($tipsLinkAttributes, 'href="#"') && !str_contains($tipsLinkAttributes, 'href=""'))
                <p>
                    <a class="float-end" {!! $tipsLinkAttributes !!}>
                        {{ t('Know more') }} <i class="fa fa-angle-double-right"></i>
                    </a>
                </p>
                @endif
            </div>
        </div>
    </div>
</aside>
