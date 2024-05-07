<div class="grid lg:grid-cols-2">

    <div class="px-4 md:px-10 pt-8">

        <p class="text-xl font-medium">Order Summary</p>

        @if($order_type == "memberships")

        <p class="text-gray-400">Check your selected membership details before subscription.</p>

        <div class="mt-8 space-y-3 rounded-lg border bg-white px-2 py-4 sm:px-6">
            <div class="flex flex-col rounded-lg bg-white sm:flex-row">
                <div class="m-2 h-fit w-28 rounded-md object-cover object-center">
                    {!! $data->icon !!}
                </div>
                <div class="flex w-full flex-col px-4 py-1">
                    <span class="font-semibold">Subscription - {{ $data->name }}</span>
                    <span class="float-right text-gray-400 line-clamp-3">{{ $data->description }}</span>
                    <p class="text-lg font-bold">{{ $data->currency->symbol }}.{{ $data->amount }}</p>
                </div>
            </div>
        </div>

        @endif

        @if($order_type == "ad-promotions")

        <p class="text-gray-400">Check your selected promotion details before subscription.</p>

        <div class="mt-8 space-y-3 rounded-lg border bg-white px-2 py-4 sm:px-6">
            <div class="flex flex-col rounded-lg bg-white sm:flex-row">
                <div class="m-2 text-2xl text-{{ $data->ribbon }}-500 font-bold flex items-center text-center px-3 sm:px-0">     
                    @if($data->packge_type == "Top ads")
                        <div class="m-2 h-fit w-16 rounded-md object-cover object-center">
                            @include('ad-promotion-icons.top-ad')
                        </div>
                    @elseif($data->packge_type == "Bump Ads")
                        <div class="m-2 h-fit w-16 rounded-md object-cover object-center">
                            @include('ad-promotion-icons.bump-ad')
                        </div>
                    @else
                        <div class="w-fit h-fit">{{ $data->packge_type }}</div>
                    @endif
                </div>
                <div class="flex w-full flex-col px-4 py-1">
                    <div class="text-lg font-semibold">
                        <span>Promotion - {{ $data->name }} </span>
                        <span class="text-xs rounded bg-gray-700 text-white p-1">{{ $data->short_name }}</span>
                    </div>
                    <span class="float-right text-gray-400 line-clamp-3">
                        After activation, promotion is valid only for {{ $data->promo_duration }} days. But the ad will be displayed until {{ $data->duration }} days from the activation date.
                    </span>
                    <p class="text-lg font-bold">{{ $data->currency->symbol }}.{{ $data->price }}</p>
                </div>
            </div>
        </div>

        @endif

    </div>

    <div class="mt-10 bg-gray-50 px-4 md:px-10 pt-8 lg:mt-0">

        <p class="text-xl font-medium">Payment Details</p>

        <p class="text-gray-400">Complete your order by providing your payment details.</p>

        <div>

            <input type="hidden" wire:model="order_id">

            <label for="email" class="mb-2 mt-4 block text-sm font-medium">Email {{ $emailDisabled ? '-' : '' }}
                <span wire:click="$toggle('emailDisabled')" class="{{ $emailDisabled ? '' : 'hidden' }} text-gray-500 hover:text-blue-700 text-xs font-normal cursor-pointer">Change</span></label>
            <div class="relative">
                <input {{ $emailDisabled ? 'disabled' : '' }} type="email" id="email" wire:model.blur="email" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="your.email@gmail.com" />
                <div class="text-xs text-red-500 mt-1">@error('email') {{ $message }} @enderror</div>
            </div>

            <label for="name" class="mb-2 mt-4 block text-sm font-medium">Card Holder {{ $nameDisabled ? '-' : '' }}
                <span wire:click="$toggle('nameDisabled')" class="{{ $nameDisabled ? '' : 'hidden' }} text-gray-500 hover:text-blue-700 text-xs font-normal cursor-pointer">Change</span></label>
            <div class="relative">
                <input {{ $nameDisabled ? 'disabled' : '' }} type="text" id="name" wire:model.blur="name" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm uppercase shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="Your full name here" />
                <div class="text-xs text-red-500 mt-1">@error('name') {{ $message }} @enderror</div>
            </div>

            <label for="contact-number" class="mb-2 mt-4 block text-sm font-medium">Contact Number {{ $contactNumberDisabled ? '-' : '' }}
                <span wire:click="toggleContactNumberDisabled" class="{{ $contactNumberDisabled ? '' : 'hidden' }} text-gray-500 hover:text-blue-700 text-xs font-normal cursor-pointer">Change</span></label>
            <div class="relative">
                @if($contactNumberDisabled)
                <select id="contact-number" wire:model.blur="contact_number" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm uppercase shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500 bg-white">
                    <option selected value="{{ $phone }}">{{ $phone }}</option>

                    @empty(!$phone_alternate_1)
                    <option value="{{ $phone_alternate_1 }}">{{ $phone_alternate_1 }}</option>
                    @endempty

                    @empty(!$phone_alternate_2)
                    <option value="{{ $phone_alternate_2 }}">{{ $phone_alternate_2 }}</option>
                    @endempty
                </select>
                @endif

                @if(!$contactNumberDisabled)
                <input type="text" id="contact_number" wire:model.blur="contact_number" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm uppercase shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="Your contact number: +94XXXXXXXXXX" />
                @endif
                <div class="text-xs text-red-500 mt-1">@error('contact_number') {{ $message }} @enderror</div>
            </div>

            @if($order_type == "memberships")

            <!-- Total -->
            <div class="mt-6 border-t pt-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">Subtotal</p>
                    <p class="font-semibold text-gray-900">{{ $data->currency->symbol }}.{{ $data->amount }}</p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Total</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $data->currency->symbol }}.{{ $data->amount }}</p>
            </div>

            @endif

            @if($order_type == "ad-promotions")

            <!-- Total -->
            <div class="mt-6 border-t pt-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">Subtotal</p>
                    <p class="font-semibold text-gray-900">{{ $data->currency->symbol }}.{{ $data->price }}</p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Total</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $data->currency->symbol }}.{{ $data->price }}</p>
            </div>

            @endif

        </div>

        <button id="membership-payment-bt" wire:loading.remove @if($order_type=="memberships" ) wire:click="proceedMembershipPayment" @endif @if($order_type=="ad-promotions" ) wire:click="proceedPackagePayment" @endif class="mb-8 mt-4 w-full rounded-md bg-gray-900 px-6 py-3 font-medium text-white">
            Proceed Payment
        </button>

        <button id="loading-membership-payment-bt" wire:loading.inline-block @if($order_type=="memberships" ) wire:target="proceedMembershipPayment" @endif @if($order_type=="ad-promotions" ) wire:target="proceedPackagePayment" @endif class="mb-8 mt-4 w-full rounded-md bg-gray-900 px-6 py-3 font-medium text-white">
            <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
            </svg>
            Loading...
        </button>

        @empty(!$error_output)
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <span class="sr-only">Info</span>

            <div>{{ $error_output }}</div>
        </div>
        @endempty

    </div>
</div>
