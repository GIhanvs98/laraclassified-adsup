<div class="input-group block w-full" style="margin-top: 20px;">

    <div class="p-4 border border-b-0 border-gray-300 flex">
        <div class="w-full">
            <div wire:ignore class="text-sm text-gray-600 font-bold"  id="max-images">
                Add upto 8 images
            </div>
            <div class="mt-1 text-xs text-gray-500">
                You can drag, drop, sort or remove images. The 1<sup>st</sup> image allways will be the thmbnail.
            </div>
        </div>
        <div class="w-max">
            <button type="button" class="image-upload-button w-max text-gray-600 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 me-2">
                Upload image
            </button>
        </div>
    </div>

    <div wire:ignore id="drop-zone" class="m-0 p-8 bg-gray-50 border border-gray-300 block cursor-pointer">

        <input type="file" id="image-input" class="mb-4 hidden" multiple>

        <div id="image-preview" class="flex flex-wrap"></div>

        <div id="image-upload-intro" class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold hover:underline image-upload-button">Click to upload</span> or drag and drop images to the Ad</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, JPEG or JPE (MAX 2MB)</p>
        </div>
    </div>

    <div class="mx-auto text-xs text-gray-500 p-4 border border-t-0 border-gray-300 flex justify-between">
        <div class="w-fit text-red-600 block images-upload-error invisible">
            @error('images') {{ $message }} @enderror
            @error('images.*') {{ $message }} @enderror
            @error('imagesCount') {{ $message }} @enderror
        </div>
        <div wire:ignore id="images-count" class="w-fit block">0 images</div>
    </div>

    <style>
        .absolute{
            position: absolute
        }

        .relative{
            position: relative
        }

        .right-1{
            right: 0.25rem
        }

        .top-1{
            top: 0.25rem
        }

        .m-2{
            margin: 0.5rem
        }

        .mx-auto{
            margin-left: auto;
            margin-right: auto
        }

        .my-8{
            margin-top: 2rem;
            margin-bottom: 2rem
        }

        .mb-2{
            margin-bottom: 0.5rem
        }

        .mb-4{
            margin-bottom: 1rem
        }

        .me-2{
            margin-inline-end: 0.5rem
        }

        .mt-1{
            margin-top: 0.25rem
        }

        .mt-4{
            margin-top: 1rem
        }

        .block{
            display: block
        }

        .flex{
            display: flex
        }

        .hidden{
            display: none
        }

        .h-24{
            height: 6rem
        }

        .h-8{
            height: 2rem
        }

        .w-24{
            width: 6rem
        }

        .w-8{
            width: 2rem
        }

        .w-max{
            width: max-content
        }

        .cursor-pointer{
            cursor: pointer
        }

        .flex-col{
            flex-direction: column
        }

        .flex-wrap{
            flex-wrap: wrap
        }

        .items-center{
            align-items: center
        }

        .justify-center{
            justify-content: center
        }

        .justify-between{
            justify-content: space-between
        }

        .rounded{
            border-radius: 0.25rem
        }

        .rounded-lg{
            border-radius: 0.5rem
        }

        .border{
            border-width: 1px
        }

        .border-b-0{
            border-bottom-width: 0px !important;
        }

        .border-t-0{
            border-top-width: 0px !important;
        }

        .border-gray-300{
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity))
        }

        .bg-gray-100{
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity))
        }

        .bg-gray-50{
            --tw-bg-opacity: 1;
            background-color: rgb(249 250 251 / var(--tw-bg-opacity))
        }

        .bg-white{
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity))
        }

        .object-cover{
            object-fit: cover
        }

        .p-2{
            padding: 0.5rem
        }

        .p-4{
            padding: 1rem !important;
        }

        .p-8{
            padding: 2rem !important;
        }

        .px-5{
            padding-left: 1.25rem;
            padding-right: 1.25rem
        }

        .py-2{
            padding-top: 0.5rem;
            padding-bottom: 0.5rem
        }

        .py-2\.5{
            padding-top: 0.625rem;
            padding-bottom: 0.625rem
        }

        .text-sm{
            font-size: 0.875rem;
            line-height: 1.25rem
        }

        .text-xs{
            font-size: 0.75rem;
            line-height: 1rem
        }

        .font-bold{
            font-weight: 700
        }

        .font-medium{
            font-weight: 500
        }

        .font-semibold{
            font-weight: 600
        }

        .text-gray-500{
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity))
        }

        .text-gray-600{
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity))
        }

        .text-red-600{
            --tw-text-opacity: 1;
            color: rgb(220 38 38 / var(--tw-text-opacity))
        }

        .text-white{
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity))
        }

        .ring-1{
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
        }

        .ring-gray-200{
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(229 231 235 / var(--tw-ring-opacity))
        }

        .blur{
            --tw-blur: blur(8px);
            filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
        }

        .hover\:bg-gray-100:hover{
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity))
        }

        .hover\:underline:hover{
            -webkit-text-decoration-line: underline;
            text-decoration-line: underline
        }

        .hover\:shadow-lg:hover{
            --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
        }

        .hover\:shadow-blue-500\/50:hover{
            --tw-shadow-color: rgb(59 130 246 / 0.5);
            --tw-shadow: var(--tw-shadow-colored)
        }

        .hover\:ring-2:hover{
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
        }

        .hover\:ring-blue-500\/50:hover{
            --tw-ring-color: rgb(59 130 246 / 0.5)
        }

        .focus\:outline-none:focus{
            outline: 2px solid transparent;
            outline-offset: 2px
        }

        .focus\:ring-4:focus{
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)
        }

        .focus\:ring-gray-200:focus{
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(229 231 235 / var(--tw-ring-opacity))
        }

        @media (prefers-color-scheme: dark){
            .dark\:text-gray-400{
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity))
            }
        }

        .sortable-placeholder {
            display: block;
            height: 6rem;
            margin: .5rem;
            width: 6rem;
            border-radius: 0.25rem;
            --tw-bg-opacity: 1;
            background-color: rgb(191 219 254 / var(--tw-bg-opacity));
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            --tw-ring-color: rgb(59 130 246 / 0.5);
        }
    </style>

</div>