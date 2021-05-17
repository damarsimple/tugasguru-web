<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;

class RemoveMeetingAttachmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:rmattachment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove all attachment from meeting';

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
        Attachment::where('attachable_type', Meeting::class)->chunk(100, function ($attachments) {
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
        });

        return 0;
    }
}
