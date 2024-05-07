<div class="col-md-9 page-content">
    <div class="inner-box">
        <h2 class="title-2"><i class="bi bi-person-check"></i> Membership </h2>
        
        <div style="clear:both"></div>


            @if($membershipDoesntExist)
                <div style="display: flex; justify-content: center; align-items: center; padding: 0px 12px 12px 12px;">
                    You do not have a membership. Please purchase a membership to obtain this feature.
                </div>
            @else
                @if($transactionsValidDoesntExist)
                    <div style="display: flex; justify-content: center; align-items: center; padding: 0px 12px 12px 12px;">
                        Your payment is overdue. Please renew your membership to avoid service disruption.
                    </div>
                @else
                    {{-- Members's Shop --}}    
                    <form name="details" class="form-horizontal">
                        
                        {{-- Membership --}}
                        <div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Membership</label>
                            <div class="col-md-9 col-lg-8 col-xl-6" style="display: flex;align-items: center;">
                                
                                @if(isset($user->membership->icon) && !empty($user->membership->icon) )
                                    <div style="width: 18px;">{!! $user->membership->icon !!}</div>&nbsp;
                                @endif

                                {{ $user->membership->name }}
                            </div>
                        </div>

                        <div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Details</label>
                            <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                {{ $user->membership->description }}
                            </div>
                        </div>

                        <div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Allowed ads per category</label>
                            <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                @if(isset($user->membership->allowed_ads))
                                    {{ $user->membership->allowed_ads }}
                                @else
                                    &#8734;
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Allowed images per category</label>
                            <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                @if(isset($user->membership->allowed_pictures))
                                    {{ $user->membership->allowed_pictures }}
                                @else
                                    &#8734;
                                @endif
                            </div>
                        </div>

                        {{--<div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Doorstep delivery</label>
                            <div class="col-md-9 col-lg-8 col-xl-6" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                @if($user->membership->doorstep_delivery == 1)
                                    true
                                @else
                                    false
                                @endif
                            </div>
                        </div>--}}


                        <div class="row mb-3 required">
                            <label class="col-md-3 col-form-label" for="membership">Giveaway promotions</label>
                            <div class="col-md-9 col-lg-8" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                
                                @if ($totalPackagesCount > 0)
                                    <table id="giveawayPackages" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable" style="width:100%;" data-filter="#filter" data-filter-text-only="true">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Promo Duration</th>
                                                <th>Duration</th>
                                                <th>Count</th>
                                                <th>Used</th>
                                                <th>Remaining</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($giveawayPackages as $key => $package)
                                                @if($package)

                                                    @php 
                                                    
                                                    $packageId = $package['id'];

                                                    $packageCount = $package['count'];

                                                    $usedPackageCount = \App\Models\Transaction::valid('ad-promotion')->where('user_id', auth()->user()->id)->where('package_id', $packageId)->giveawayTransactions()->count();

                                                    $getawayPackages = \App\Models\Package::find($package['id']); 
                                                    
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $key+1 }}</td>
                                                        <td>{{ $getawayPackages->name }}</td>
                                                        <td>{{ $getawayPackages->promo_duration }}</td>
                                                        <td>{{ $getawayPackages->duration }}</td>
                                                        <td>{{ $packageCount }}</td>
                                                        <td>{{ $usedPackageCount }}</td>
                                                        <td>{{ $packageCount - $usedPackageCount }}</td>
                                                    </tr>

                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                @else
                                    You don't have any giveaway promotions
                                @endif
                            </div>
                        </div>

                        @if ($totalPackagesCount > 0)
                            <div class="row mb-3 required">
                                <label class="col-md-3 col-form-label" for="membership">Giveaway promotions used for</label>
                                <div class="col-md-9 col-lg-8" style="padding-top: calc(0.375rem + 1px);padding-bottom: calc(0.375rem + 1px);">
                                    
                                    @if($totalUsedPackageCount > 0)
                                        <table id="giveawayPackages" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable" style="width:100%;" data-filter="#filter" data-filter-text-only="true">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Photo</th>
                                                    <th>Listing Details</th>
                                                    <th>Option</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($giveawayPackages as $key => $package)
                                                    @if($package)

                                                        @php 
                                                        
                                                        $packageId = $package['id'];

                                                        $usedOnes = \App\Models\Transaction::valid('ad-promotion')->where('user_id', $user->id)->where('package_id', $packageId)->giveawayTransactions()->first();

                                                        @endphp

                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td style="width:20%" class="add-img-td">
                                                                <a href="{{ \App\Helpers\UrlGen::post($usedOnes->post) }}">
                                                                    @if(\App\Models\ThumbnailImage::where('post_id', data_get($usedOnes->post, 'id'))->exists())
                                                                        <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ asset(config('pictures.thumbnail_image.image_location') . '/' . \App\Models\ThumbnailImage::where('post_id', $usedOnes->post->id)->first()->filename) }}" alt="{{ $usedOnes->post->title }}">
                                                                    @else
                                                                        <img class="img-thumbnail no-margin" style="border: 0px;" src="{{ \Illuminate\Support\Facades\Storage::url(config('larapen.core.picture.default')) }}">
                                                                    @endif
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <p>
                                                                        <strong>
                                                                            <a href="{{ \App\Helpers\UrlGen::post($usedOnes->post) }}" title="{{ $usedOnes->post->title }}">{{ str($usedOnes->post->title)->limit(40) }}</a>
                                                                        </strong>
                                                                        @if (in_array($pagePath, ['list', 'archived', 'pending-approval']))
                                                                            @if (!empty(data_get($usedOnes->post, 'latestPayment')) && !empty(data_get($usedOnes->post, 'latestPayment.package')))
                                                                                @php
                                                                                    if (data_get($usedOnes->post, 'featured') == 1) {
                                                                                        $color = data_get($post, 'latestPayment.package.ribbon');
                                                                                        $packageInfo = '';
                                                                                    } else {
                                                                                        $color = '#ddd';
                                                                                        $packageInfo = ' (' . t('Expired') . ')';
                                                                                    }
                                                                                @endphp
                                                                                <i class="fa fa-check-circle"
                                                                                    style="color: {{ $color }};"
                                                                                    data-bs-placement="bottom"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="{{ data_get($usedOnes->post, 'latestPayment.package.short_name') . $packageInfo }}"
                                                                                ></i>
                                                                            @endif
                                                                        @endif
                                                                    </p>
                                                                    <p>
                                                                        <strong>Package type: </strong>{{ $usedOnes->package->name }} - {{ $usedOnes->package->promo_duration }} days
                                                                    </p>
                                                                </div>   
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <p>
                                                                        <a class="btn btn-danger btn-sm" wire:click="remove({{$usedOnes->id}})">
                                                                            <i class="fa fa-trash"></i> Remove
                                                                        </a>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    @endif
                                                @endforeach

                                            </tbody>
                                        </table>
                                    @else
                                        You don't have used any giveaway promotions
                                    @endif

                                </div>
                            </div>
                        @endif

                    </form>
                @endif
            @endif

        
            {{-- Membership --}}
            @if(isset($user->membership))
        
            @else
                <div class="text-center mt10 mb30">You do not have a membership.</div>
            @endif

        
        <div style="clear:both"></div>
    
    </div>
</div>