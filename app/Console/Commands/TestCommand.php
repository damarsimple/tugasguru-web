<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Booking;
use App\Models\User;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        // $bookie = User::find(3);
        // $booker = User::find(1);

        $booking = new Booking();

        $booking->user_id = 3;
        $booking->teacher_id = 1;
        $booking->start_at = now();
        $booking->finish_at = now()->addDay(10);


        $booking->save();

        // $users = User::whereHas('subjects', function ($q) {
        //     return $q->where('subjects.id', 1);
        // });

        // dd($users->get()->count());

        // $attachments = Attachment::all();

        // $attachment = $attachments->last();



        // $media =   FFMpeg::open($attachment->path);

        // print($media->getDurationInSeconds() . PHP_EOL);


        return 0;
    }
}
