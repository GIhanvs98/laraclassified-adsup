<!-- 
    filed error response: name 
    field model: name.value
    field properties: value, disabled
-->
<div class="input-group" style="margin-top: 0px;">

    <div class="text-xs text-gray-500">Name</div>
    
    <div class="flex w-full items-center justify-items-start">
        
        <input wire:model.blur="name.value" name="name" @if($name['disabled'] && auth()->check()) disabled @endif type="text" placeholder="Your name: Jhon Doe" class="border mt-1 mb-1 p-2 w-80 @if($name['disabled'] && auth()->check()) bg-gray-100 @endif" style="font-size: 14px; border-radius: 5px;">
        
        @if(option('allow-guest-ads') && (!$name['disabled'] || !auth()->check()))
            
            <div wire:click="$set('name.disabled', false)" class="text-xs text-blue-500 ml-1 cursor-pointer w-fit px-4">Change</div> 
        
        @endif
        
    </div>

    @error('name.value') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

</div>