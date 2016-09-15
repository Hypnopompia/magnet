<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class generateimportpinjobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generateimportpinjobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find boards that need to be imported and schedule a job for them.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNotNull('pinterestaccesstoken')->get();

        if (!$users) {
            return;
        }

        foreach ($users as $user) {
            // Get the first board that either hasn't been imported or hasn't been updated in more than a day
            $board = $user->boards()
                ->where(function($q){
                    $q->where('refreshed_at', '<', Carbon::now()->subDay() )
                    ->orWhereNull('refreshed_at');
                })
                ->orderBy('refreshed_at')
                ->orderBy('updated_at')
                ->first();

            if ($board) {
                dispatch(new ImportPins($board));
            }
        }
    }
}
