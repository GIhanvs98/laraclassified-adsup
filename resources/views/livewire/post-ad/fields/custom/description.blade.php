<div class="input-group" style="margin-top: 30px;">
    <div class="text-xs text-gray-500 mb-1 flex justify-between w-full">
        <div>Description</div>
        <div>&nbsp;&nbsp;{{ $content_length }}/{{ config('content-component.length.max') }}</div>
    </div>
    <div wire:ignore class="w-full block">
        <textarea id="content" wire:model.blur="content" placeholder="Your Ad Description..." rows="6" cols="40" class="border w-full mt-1 mb-1 p-2 h-48" style="font-size: 14px;"></textarea>
    </div>
    @error('content') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>