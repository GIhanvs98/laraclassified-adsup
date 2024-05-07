<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteExpiredAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired-ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired ads which can by any type of ad.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // For All the types of ads.

        try {
            // Run the ad-promotion deactivation process.

            $now = Carbon::now();

            // Fetch all posts
            $posts = Post::all();

            // Iterate through each post and call the method
            foreach ($posts as $post) {
                Post::deletePostWithoutPackageOrExpiredDuration($post);
            }

            // Process completed successfull.

            $this->info('Ad delete completed successfully on ' . $now . '.');

        } catch (\Throwable $th) {
            // Return error.

            $this->error('Ad Delete Error: ' . $th->getMessage());
        }
    }
}
