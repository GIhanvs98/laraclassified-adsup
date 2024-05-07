<form wire:submit="register" class="form-horizontal">

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label">Name <sup>*</sup></label>
        <div class="col-md-9 col-lg-6">
            <input wire:model.blur="name" name="name" placeholder="Name" class="form-control" type="text">
            @error('name')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label">Email <sup>*</sup></label>
        <div class="col-md-9 col-lg-6">
            <input wire:model.blur="email" name="email" placeholder="Email Address" class="form-control" type="email">
            @error('email')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if ($success)
        <div class="row mb-3 required" style="margin-bottom: 5px !important;">
            <div class="col-md-3"></div>
            <div class="col-md-9 col-lg-6">
                <a class="text-xs" wire:click="editContactNumber">Change phone number</a>
            </div>
        </div>
    @endif
    
    @if ($step_1)
    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label">Phone Number <sup>*</sup></label>
        <div class="col-md-9 col-lg-6">
            <input wire:model.blur="phone" placeholder="+94XXXXXXXXX" name="tel-national" class="form-control" type="tel">
            @error('phone')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>
    @endif

    @if ($step_2)
    <div class="row mb-3 required">
        <label for="otp" class="col-md-3 col-form-label">OTP number(6 digits)</label>
        <div class="col-md-9 col-lg-6">
            <input type="number" wire:model.live="otp" name="otp" id="otp" class="form-control" placeholder="OTP number" style="font-size: 14px;">
            @error('otp') 
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
            <div class="text-xs font-medium mt-2">
                Didn't receive yet? <a class="text-blue-600 hover:underline dark:text-blue-500" wire:click="resendOTP">Resend OTP</a>
            </div>
        </div>
    </div>
    @endif

    @if ($success)
    <div class="row mb-3 required">
        <label for="verifiedPhone" class="col-md-3 col-form-label">Phone Number <sup>*</sup></label>
        <div class="col-md-9 col-lg-6">
            <input type="tel" value="{{ $phone }}" name="verified-phone" id="verifiedPhone" class="form-control" readonly style="font-size: 14px;">
            <div class="text-xs mt-2" style="color: green;">
                Verified!
            </div>
        </div>
    </div>
    @endif

    <div class="row required" style="position: relative; top: -10px;">
        <div class="col-md-3"></div>
        <div class="col-md-9 col-lg-6">

            <button wire:loading.flex wire:target="send" type="submit" class="btn btn-secondary btn-sm">
                <div role="status">
                    <svg aria-hidden="true" style="height: 12px; width: auto; margin-right: 8px;" class="inline text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
                Sending
            </button>

            <button wire:loading.flex wire:target="verify" type="submit" class="btn btn-secondary btn-sm">
                <div role="status">
                    <svg aria-hidden="true" style="height: 12px; width: auto; margin-right: 8px;" class="inline text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
                Verifying
            </button>

            @if ($step_1)
            <button wire:loading.remove wire:target="send" type="button" wire:click="send" class="btn btn-secondary btn-sm">
                <span class="block" style="width: max-content;">Send OTP</span>
            </button>
            @endif

            @if ($step_2)
            <button wire:loading.remove wire:target="verify" type="button" wire:click="verify" class="btn btn-secondary btn-sm">
                <span class="block" style="width: max-content;">Verify now</span>
            </button>
            @endif
        </div>
    </div>

    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label">Password</label>
        <div class="col-md-9 col-lg-6">
            <input wire:model.blur="password" id="password" name="password" placeholder="Password" class="form-control" type="password">
            @error('password')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>


    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label"></label>
        <div class="col-md-9 col-lg-6">
            <input wire:model.blur="password_confirmation" placeholder="Password Confirmation" name="password_confirmation" class="form-control" type="password">
            @error('password_confirmation')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-1 required">
        <label class="col-md-3 col-form-label"></label>
        <div class="col-md-9">
            <div class="form-check" style="display: flex;align-items: center;padding: 0px;">
                <input wire:model.change="accept_terms" id="acceptTerms" value="1" type="checkbox">
                <label class="form-check-label" for="acceptTerms" style="font-weight: normal;margin: 0px 0px 0px 10px;">
                    {!! t('accept_terms_label', ['attributes' => getUrlPageByType('terms')]) !!}
                </label>
            </div>
            @error('accept_terms')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label"></label>
        <div class="col-md-9">
            <div class="form-check" style="display: flex;align-items: center;padding: 0px;">
                <input wire:model.change="accept_marketing_offers" id="acceptMarketingOffers" value="1" type="checkbox">
                <label class="form-check-label" for="acceptMarketingOffers" style="font-weight: normal;margin: 0px 0px 0px 10px;">
                    {!! t('accept_marketing_offers_label') !!}
                </label>
            </div>
            @error('accept_marketing_offers')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-md-3 col-form-label"></label>
        <div class="col-md-7">
            <button type="submit" class="btn btn-primary btn-md">Register</button>
        </div>
    </div>

</form>
