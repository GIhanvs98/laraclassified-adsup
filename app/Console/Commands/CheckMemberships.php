<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Membership;
use Illuminate\Support\Carbon;

class CheckMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired memberships inside transactions and memberships.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // For `Memberships`.
        try {
            Transaction::deactivateMembershipTransactions();
            $this->info('Membership deactivation process completed successfully on ' . Carbon::now() . '.'); // Log or display success message
        } catch (\Throwable $th) {
            $this->error('Membership Deactivation Error: ' . $th->getMessage()); // Log or display error message
        }
    }
}
