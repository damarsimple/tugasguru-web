<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
class EmulateXenditSuccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xendit:emulate {id}';

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
            $transaction = Transaction::where('id', $this->argument('id'))->firstOrFail();
             $transaction->is_paid = true;
            $transaction->status = Transaction::SUCCESS;
            $transaction->invoice_response = [];

            $transaction->save();
        return 0;
    }
}
