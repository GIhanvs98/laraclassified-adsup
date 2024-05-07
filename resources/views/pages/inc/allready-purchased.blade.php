<section class="flex items-center h-full p-16 dark:bg-gray-900 dark:text-gray-100">
    <div class="container flex flex-col items-center justify-center px-5 mx-auto my-8">
        <div class="max-w-md text-center">

            <div class="w-full h-20 text-blue-700 flex justify-center mb-6">
                <svg class="w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            @if ($category == "memberships")
                    
                <p class="text-2xl font-semibold md:text-3xl">You have allready purchased a membership.</p>

                <p class="mt-4 mb-8 text-gray-700 dark:text-gray-400 text-md">{{ $message }}</p>

                <a rel="noopener noreferrer" href="{{ route('home') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back to homepage</a>

            @endif
            
            @if ($category == "ad-promotions")
                    
                <p class="text-2xl font-semibold md:text-3xl">You have allready promoted the ad.</p>

                <p class="mt-4 mb-8 text-gray-700 dark:text-gray-400 text-md">{{ $message }}</p>

                <a rel="noopener noreferrer" href="{{ route('home') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back to homepage</a>

            @endif

		</div>
	</div>
</section>