<div class="w-fit">
    @if(isset($showable_contact_numbers) && count($showable_contact_numbers) > 0)
        <div wire:click="add" class="input-group flex mb-3 mt-2 text-green-600 w-fit items-center justify-center cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>Add another contact number</div>
        </div>
    @endif
</div>

<style>
.mb-3{
  margin-bottom: 0.75rem
}

.mt-2{
  margin-top: 0.5rem;
}

.mr-1{
  margin-right: 0.25rem
}

.flex{
  display: flex
}

.h-6{
  height: 1.5rem
}

.w-6{
  width: 1.5rem
}

.w-fit{
  width: fit-content
}

.cursor-pointer{
  cursor: pointer
}

.items-center{
  align-items: center
}

.justify-center{
  justify-content: center
}

.text-green-600{
  --tw-text-opacity: 1;
  color: rgb(22 163 74 / var(--tw-text-opacity))
}
</style>
