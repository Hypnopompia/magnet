<?php

namespace App\Jobs;

use Log;
use App\Board;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPins implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $board;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('importPinsJob', ['board' => $this->board]);
        $this->board->importPins();
    }
}
