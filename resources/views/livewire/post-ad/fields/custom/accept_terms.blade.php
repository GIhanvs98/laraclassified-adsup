<div class="input-group block" style="margin-top: 15px;">
    <div class="text-xs text-gray-500 flex"></div>
    <label class="flex w-fit" style="cursor: pointer;">
        <input wire:model="accept_terms" type="checkbox" class="border mt-1 mb-1 p-2" style="font-size: 14px;cursor: pointer;">
        <div class="text-xs text-gray-800 flex" style="margin-top: 6px;margin-left: 10px;cursor: pointer;">Agree to Terms and Conditions</div>
    </label>
    @error("accept_terms") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
</div>