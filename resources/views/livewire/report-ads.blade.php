<form wire:submit.prevent="send" class="relative w-full max-w-md max-h-full">

    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-start justify-between p-4 rounded-t">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" style="padding: 0px;">
                Report this ad?
            </h3>
            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="reportAdModal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <!-- Modal body -->
        <div class="p-6 space-y-6" style="padding-top: 0px;">
            
            <p class="text-sm text-gray-600">
                We're always striving to ensure our ads meet high standards, and we greatly appreciate feedback from our users.
            </p>

            @if($success == "1")
                <div class="mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <svg class="mr-3 inline h-4 w-4 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div><span class="font-medium">Success alert!</span> Ad report sent successfully!</div>
                </div>
            @endif

            @if($success == "0")
                <div class="mb-4 flex items-center rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <svg class="mr-3 inline h-4 w-4 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div><span class="font-medium">Danger alert!</span> Unexpected error! Please try again.</div>
                </div>
            @endif

            <input type="hidden" wire:model="post_id">

            <div class="input-group">
                <div class="text-xs text-gray-500">Reason</div>
                <select wire:model="reason" class="border w-full mt-1 mb-1 p-2 bg-transparent " style="font-size: 14px;">
                    <option selected disabled value="">Reason</option>
                    <option value="sold-out-or-unavailable">Item sold/unavailable</option>
                    <option value="fraud">Fraud</option>
                    <option value="duplicate">Duplicate</option>
                    <option value="spam">Spam</option>
                    <option value="wrong-category">Wrong category</option>
                    <option value="offensive">Offensive</option>
                    <option value="other">Other</option>
                </select>
                <div class="text-xs text-red-500">@error('reason') {{ $message }} @enderror</div>
            </div>

            <div class="input-group">
                <div class="text-xs text-gray-500">Email</div>
                <input wire:model="email" type="email" placeholder="Your Email" class="border w-full mt-1 mb-1 p-2 " style="font-size: 14px;">
                <div class="text-xs text-red-500">@error('email') {{ $message }} @enderror</div>
            </div>

            <div class="input-group">
                <div class="text-xs text-gray-500">Message</div>
                <textarea wire:model="message" placeholder="Type your message here..." name="message" rows="6" cols="40" class="border w-full mt-1 mb-1 p-2 " style="font-size: 14px;"></textarea>
                <div class="text-xs text-red-500">@error('message') {{ $message }} @enderror</div>
            </div>

            <button id="closeReportAdModal" wire:click="resetAds()" hidden data-modal-hide="reportAdModal">Close</button>
            <button type="submit" class="btn btn-success btn-block">Send</button>
            
            @if($success == "1")
                <script>
                    document.getElementById('closeReportAdModal').click();
                    const closeReportAdModal = setTimeout(function () {
                        document.getElementById('closeReportAdModal').click();
                    }, 2000);
                </script>
            @endif
        </div>
    </div>
</form>