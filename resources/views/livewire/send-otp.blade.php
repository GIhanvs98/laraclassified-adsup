<div class="h-fit flex items-bottom" style="{{ $parentStyle }}">

    @if ($step_1)
    <div class="w-full">
        <label for="phone" class="{{ $labelClass }}">Your {{ $title }}</label>
        <input type="tel" wire:model.blur="phone" name="tel-national" id="phone" class="{{ $inputClass }}" placeholder="07XXXXXXXX" style="font-size: 14px;">
        <div>
            @error('phone') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
        </div>
    </div>
    @endif

    @if ($step_2)
    <div class="w-full">
        <label for="otp" class="{{ $labelClass }}">OTP number(6 digits)</label>
        <input type="number" wire:model.live="otp" name="otp" id="otp" class="{{ $inputClass }}" placeholder="OTP number" style="font-size: 14px;">
        <div>
            @error('otp') <span class="text-xs mt-2 text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="text-sm font-medium text-gray-900 dark:text-white mt-2">
            Didn't receive yet? <a class="text-blue-600 hover:underline dark:text-blue-500" wire:click="resendOTP">Resend OTP</a>
        </div>
    </div>
    @endif

    @if ($success)
    <div class="w-full">
        <label for="verifiedPhone" class="{{ $labelClass }}">Your {{ $title }}</label>
        <input type="tel" value="{{ $phone }}" name="verified-phone" id="verifiedPhone" class="{{ $readonlyClass }}" readonly style="font-size: 14px;">
        <div class="text-xs mt-2 text-green-500">
            Verified!
        </div>
    </div>
    @endif

    <button wire:loading.flex wire:target="send" type="submit" class="{{ $buttonClass }}" style="position: relative; top: 25px;">
        <div role="status">
            <svg aria-hidden="true" class="inline w-4 h-4 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
            </svg>
            <span class="sr-only">Loading...</span>
        </div>
        Sending
    </button>

    <button wire:loading.flex wire:target="verify" type="submit" class="{{ $buttonClass }}" style="position: relative; top: 25px;">
        <div role="status">
            <svg aria-hidden="true" class="inline w-4 h-4 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
            </svg>
            <span class="sr-only">Loading...</span>
        </div>
        Verifying
    </button>

    @if ($step_1)
    <button wire:loading.remove wire:target="send" type="button" wire:click="send" class="{{ $buttonClass }}" style="position: relative; top: 25px;">
        <span class="block" style="width: max-content;">Send OTP</span>
    </button>
    @endif

    @if ($step_2)
    <button wire:loading.remove wire:target="verify" type="button" wire:click="verify" class="{{ $buttonClass }}" style="position: relative; top: 25px;">
        <span class="block" style="width: max-content;">Verify now</span>
    </button>
    @endif

</div>