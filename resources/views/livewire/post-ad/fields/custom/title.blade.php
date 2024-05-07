<div class="input-group" style="margin-top: 5px;">
    <div class="text-xs text-gray-500">Ad Title</div>
    <div class="flex w-full items-center justify-center">
        <input wire:model.blur="title" type="text" placeholder="Your Ad Title" class="border w-full mt-1 mb-1 p-2" style="font-size: 14px; border-radius: 5px;">
    </div>
    @error('title') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
</div>