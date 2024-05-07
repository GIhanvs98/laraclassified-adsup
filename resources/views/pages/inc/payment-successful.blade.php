<section class="flex items-center h-full p-16 dark:bg-gray-900 dark:text-gray-100">
	<div class="container flex flex-col items-center justify-center px-5 mx-auto my-8">
		<div class="max-w-md text-center">

      <div class="w-full h-20 text-green-600 flex justify-center mb-6">
        <svg class="w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
      </div>

			<p class="text-2xl font-semibold md:text-3xl">Payment Successful!</p>

			<p class="mt-4 mb-8 text-gray-700 dark:text-gray-400">{{ $message }}</p>

      @if ($category == "memberships")
          <a rel="noopener noreferrer" href="{{ route('account') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">View dashboard</a>
      @endif
      
      @if ($category == "ad-promotions")
          <a rel="noopener noreferrer" href="{{ route('home') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Back to home page</a>
      @endif
		</div>
	</div>
</section>