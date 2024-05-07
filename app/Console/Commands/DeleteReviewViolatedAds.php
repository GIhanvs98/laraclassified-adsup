<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DeleteReviewViolatedAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:review-violated-ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all the ads which are not met the standard rules and requlations, which are revived by the admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Run the deactivation process.

            $now = Carbon::now();

            Post::whereHas('reviewingViolation', function (Builder $query) {
                $query->notRecheckedTimeout();
            })->delete();
            
            $this->info('Review violated ads deletion completed successfully on ' . $now . '.');

        } catch (\Throwable $th) {
            // Return error.

            $this->error('Review Violated Ads Delete Error: ' . $th->getMessage());
        }
    }
}
