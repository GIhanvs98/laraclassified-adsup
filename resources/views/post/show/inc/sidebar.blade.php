@php
    $post ??= [];
    $user ??= [];
    $countPackages ??= 0;
    $countPaymentMethods ??= 0;

    // Validate the shop existance.
    $shopExists = \App\Models\Shop::where('user_id', $post['user_id'])->exists();

    if(isset($post['user_id']) && !empty($post['user_id'])){

        $membership_id = \App\Models\User::find($post['user_id'])->membership_id;   

        // Validate membership not a free membership.
        $membershipExists = \App\Models\Membership::where('id', $membership_id)->member()->exists();

    }else{

        $membershipExists = false;
    }

    $nonMember = \App\Models\Membership::whereId(config('subscriptions.memberships.default_id'))->nonMember()->first();

    // Validate transactions. If the user has paid for the given time, then it is allowed. If he hasent payed for the current time.
    $transactionsValidExists = \App\Models\Transaction::valid('membership')
													->where('user_id', $post['user_id'])
													->exists();	

@endphp

<aside style="position: sticky;top: 0px;" class="border-0 border-gray-200 border-t lg:border-t-0 border-solid">
    <div class="card card-user-info sidebar-card" style="border: 0px;border-radius: 0px;border-bottom: 1px solid rgba(0,0,0,.125);">

        <div class="block-cell user" style="border-radius: 0px 4px 0px 0px;">
            <div class="cell-media">
                @if (isset($post['user_id']) && !empty($post['user_id']) && \App\Models\User::whereId($post['user_id'])->exists())
                    <a href="{{ route('shops.index', ['id'=> $user['id'], 'slug'=> \Illuminate\Support\Str::slug($user['name'], '-')]) }}">
                        <img src="{{ data_get($post, 'user_photo_url') }}" alt="{{ data_get($post, 'contact_name') }}">
                    </a>
                @else
                    <img src="{{ data_get($post, 'user_photo_url') }}" alt="{{ data_get($post, 'contact_name') }}">
                @endif
            </div>
            <div class="cell-content">

                @if (\App\Models\User::whereId($post['user_id'])->exists())

                    @php
                        
                        $userData = \App\Models\User::whereId($post['user_id'])->with(['membership', 'shop'])->first();   

                    @endphp

                    @if($shopExists && $membershipExists && $transactionsValidExists)
                        {{-- Paid membership with shop --}}

                        <span class="name">
                            <a href="{{ route('shops.index', ['id'=> $user['id'], 'slug'=> \Illuminate\Support\Str::slug($post['contact_name'], '-')]) }}">
                                {{ $post['contact_name'] }}
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

                        <a href="{{ route('shops.index', ['id'=> $userData->shop->id, 'slug'=> \Illuminate\Support\Str::slug($userData->shop->title, '-')]) }}" class="mt-3 " style="font-size: 12px;">Visit member's shop</a>
                    
                    @else
                        {{-- Payment behind membership or dont have a shop--}}

                        <span class="name">{{ $post['contact_name'] }}</span>
                    
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

                    <div class="mt-1 text-sm" style="font-size: 10px;">Guest user since {{ date_format(date_create($post['created_at']), "F Y") }}</div>
                @endif

            </div>
        </div>

        <div class="card-content">
            <div class="ev-action" style="border-top: 0;">

                <div class="px-4 py-6" style="padding-top: 0px;padding-left: 10px !important;padding-right: 0px !important;">
                    <livewire:contact-numbers :data-id="$post['id']" type="post" />
                </div>

                <div class="flex items-center justify-start px-4 py-6" style="padding: 0px !important;padding-left: 10px !important;">
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

    <div class="d-block d-lg-none">
		<div class="content-footer text-start">
			<div class="md:inline-flex">
				{{--@if (auth()->check())
					@if (auth()->user()->id == data_get($post, 'user_id'))
						<a class="btn btn-default" href="{{ \App\Helpers\UrlGen::editPost($post) }}">
							<i class="far fa-edit"></i> {{ t('Edit') }}
						</a>
					@else
						{!! genPhoneNumberBtn($post) !!}
						{!! genEmailContactBtn($post) !!}
					@endif
				@else
					{!! genPhoneNumberBtn($post) !!}
					{!! genEmailContactBtn($post) !!}
				@endif--}}

				<a href="{{ route('post-ad.edit', ['post' => $post['id'] ]) }}" class="btn btn-default btn-block md:mb-0 mb-2">
					<i class="far fa-edit"></i>&nbsp;Edit ad
				</a>

				@if (config('settings.single.publication_form_type') == '1')
					{{--<a href="{{ url('posts/' . data_get($post, 'id') . '/photos') }}" class="btn btn-default btn-block md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fas fa-camera"></i> {{ t('Update Photos') }}
					</a>--}}
					@if ($countPackages > 0 && $countPaymentMethods > 0)
						<a href="{{  route('post-ad.promote' , ['postId' => data_get($post, 'id')]) }}" class="btn btn-success btn-block md:mb-0 mb-2" style="margin-top:0px;">
							<i class="far fa-check-circle"></i> {{ t('Make It Premium') }}
						</a>
					@endif
				@endif

				{{--@if (empty(data_get($post, 'archived_at')) && isVerifiedPost($post))
					<a href="{{ url('account/posts/list/' . data_get($post, 'id') . '/offline') }}" class="btn btn-warning btn-block confirm-simple-action md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fas fa-eye-slash"></i> {{ t('put_it_offline') }}
					</a>
				@endif--}}

				@if (!empty(data_get($post, 'archived_at')))
					<a href="{{ url('account/posts/archived/' . data_get($post, 'id') . '/repost') }}" class="btn btn-info btn-block confirm-simple-action md:mb-0 mb-2" style="margin-top:0px;">
						<i class="fa fa-recycle"></i> {{ t('re_post_it') }}
					</a>
				@endif
				<div data-modal-target="reportAdModal" data-modal-toggle="reportAdModal" class="btn btn-default btn-block md:mb-0 mb-2" style="margin-top:0px;">
					<i class="fa fa-exclamation"></i>&nbsp;Report ad
				</div>
			</div>
		</div>
    </div>

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