<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Post;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\table;

class RunBumpAd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:bump-ad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the ad according to the transaction records daily at given time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $results = Transaction::processAdPromotionTransactions();

            if(isset($results) && count($results) > 0) {
                 $styledResults = array_map(function($result){
                    if($result['successful']){
                        $result['status'] = "Success";
                    }else{
                        $result['status'] = "\e[1;31mError\e[0m"; // ANSI escape code for red color
                    }
                    return [$result['status'], $result['output']];
                }, $results);

                table(
                    ['Status', 'Response'],
                    $styledResults
                );
            }
            $this->info('Ad/Post promotion process completed successfully on ' . Carbon::now() . '.'); // Log or display success message
        } catch (\Throwable $th) {
            $this->error('`Bump Ad` Promotion Error: ' . $th->getMessage(). ' at Line: '. $th->getLine()); // Log or display error message
        }
    }
}
