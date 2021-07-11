<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test';

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

        $to_name = "Damar Albaribin";
        $to_email = 'damaralbaribin@gmail.com';

        $data = [
            'name' => 'Customer Service',
            'body' => 'A test mail',
            'subject' => 'Test Email From Tugasguru'
        ];
        Mail::send('emails.mail', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name);
            $message->from(env('MAIL_USERNAME'), 'Test Mail');
        });
        return 0;
    }
}
