<!-- 
    filed error response: email 
    field model: email.value
    field properties: value, disabled
-->
<div class="input-group" style="margin-top: 20px;">

    <div class="text-xs text-gray-500">Email</div>

    <div class="flex w-full items-center justify-items-start">

        <input wire:model.blur="email.value" name="email" @if($email['disabled'] && auth()->check()) disabled @endif type="email" placeholder="Your Email" class="border w-80 mt-1 mb-1 p-2 @if($email['disabled'] && auth()->check()) bg-gray-100 @endif" style="font-size: 14px; border-radius: 5px;">

        @if(option('allow-guest-ads') && (!$email['disabled'] || !auth()->check()))
            
            <div wire:click="$set('email.disabled', false)" class="text-xs text-blue-500 ml-1 cursor-pointer w-fit px-4">Change</div> 
    
        @endif

    </div>

    @error('email.value') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

</div>