<div class="block">

    @if(isset($mainContactNumber) && !empty($mainContactNumber))
        @if ( ( (isset($data->phone) && !empty($data->phone)) || (isset($data->phone_alternate_1) && !empty($data->phone_alternate_1)) || (isset($data->phone_alternate_2) && !empty($data->phone_alternate_2)) ) && $showAllContactNumbers)
            <div class="flex justify-start">
                <div class="w-6 h-6 fill-blue-700 mt-2 flex justify-start" style="min-width: 1.5rem;">
                    <img src="{{ asset('images/phone.svg') }}" alt="whatsapp-svg-icon">
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-bold text-gray-900 mb-2" style="padding-bottom: 0px;">Call Seller</h3>
                    <div>

                        @if(isset($data->phone) && !empty($data->phone))
                            <a class="d-inline-block text-sm font-bold text-gray-700 mb-2 bg-gray-200 w-fit px-2 py-1 rounded" href="tel:{{ $data->phone }}">{{ $data->phone }}</a>
                        @endif

                        @if(isset($data->phone_alternate_1) && !empty($data->phone_alternate_1))
                            <a class="d-inline-block text-sm font-bold text-gray-700 mb-2 bg-gray-200 w-fit px-2 py-1 rounded" href="tel:{{ $data->phone }}">{{ $data->phone_alternate_1 }}</a>
                        @endif

                        @if(isset($data->phone_alternate_2) && !empty($data->phone_alternate_2))
                            <a class="d-inline-block text-sm font-bold text-gray-700 mb-2 bg-gray-200 w-fit px-2 py-1 rounded" href="tel:{{ $data->phone }}">{{ $data->phone_alternate_2 }}</a>
                        @endif

                    </div>
                </div>
            </div>
        @else
            <div class="flex justify-start">
                <div class="w-6 h-6 fill-blue-700 mt-2 flex justify-start" style="min-width: 1.5rem;">
                    <img src="{{ asset('images/phone.svg') }}" alt="whatsapp-svg-icon">
                </div>
                <div class="ml-4">
                    <div class="text-md cursor-pointer" wire:click="showAllContactNumbersHandler" style="color: #014f84; font-size: 16px;">
                        {{ $mainContactNumber }}
                    </div>
                    <div class="text-xs text-gray-500">Click to show phone numbers</div>
                </div>
            </div>
        @endif
    @endif

    @if(isset($mainWhatsappNumber) && !empty($mainWhatsappNumber))
        @if (isset($data->whatsapp_number) && !empty($data->whatsapp_number) && $showAllWhatsappNumbers)
            <div class="mt-4 flex justify-start">
                <div class="w-6 h-6 fill-blue-700 mt-2 flex justify-start" style="min-width: 1.5rem;">
                    <img src="{{ asset('images/whatsapp.svg') }}" alt="whatsapp-svg-icon">
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-bold text-gray-900 mb-2" style="padding-bottom: 0px;">Whatsapp</h3>
                    <div>
                        <a class="d-inline-block text-sm font-bold text-gray-700 bg-gray-200 w-fit px-2 py-1 rounded" href="https://wa.me/{{ $data->whatsapp_number }}">{{ $data->whatsapp_number }}</a>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-4 flex justify-start">
                <div class="w-6 h-6 fill-blue-700 mt-2 flex justify-start" style="min-width: 1.5rem;">
                    <img src="{{ asset('images/whatsapp.svg') }}" alt="whatsapp-svg-icon">
                </div>
                <div class="ml-4">
                    <div class="text-md cursor-pointer" wire:click="showAllWhatsappNumbersHandler" style="color: #014f84; font-size: 16px;">
                        {{ $mainWhatsappNumber }}
                    </div>
                    <div class="text-xs text-gray-500">Click to show whatsapp numbers</div>
                </div>
            </div>
        @endif
    @endif
</div>
