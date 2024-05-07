<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class CheckAdPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ad-promotions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired ad-promotions inside transactions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // For both ads (Top Ads and Bump Ads).
        try {
            Transaction::deactivateAdPromotionTransactions(); // Run the ad-promotion deactivation process.
            $this->info('`Ad Promotion` deactivation process completed successfully on ' . Carbon::now() . '.'); // Process completed successfull.
        } catch (\Throwable $th) {
            $this->error('`Ad Promotion` Deactivation Error: ' . $th->getMessage()); // Return error.
        }
    }
}