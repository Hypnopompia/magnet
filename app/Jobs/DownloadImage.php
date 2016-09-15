<?php

namespace App\Jobs;

use App\Pin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadImage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $pin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Pin $pin)
    {
        $this->pin = $pin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('downloadImageJob', ['pin' => $this->pin]);
        $this->pin->downloadImage();
    }
}
