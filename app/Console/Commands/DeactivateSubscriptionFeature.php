<?php

namespace App\Console\Commands;

use App\Enum\Ability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class DeactivateSubscriptionFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:deactivate';

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


        $users = User::whereHas('rawsubscriptions', function ($e) {
            return $e->whereDate('expired_at', Carbon::today());
        })->get();

        print("found " . $users->count() . " users\n");

        foreach ($users as $user) {
            print("deactiviting " . $user->name . "\n");
            $abilities = [];

            foreach ($user->subscriptions as $subscription) {
                foreach ($subscription->ability_alt as $ability) {
                    $abilities[] = $ability;
                }
            }

            $user->access = array_unique($abilities);

            $user->save();

            foreach ($user->schools as $school) {
                $school->teachers()->updateExistingPivot(
                    $user->id,
                    [
                        'is_homeroom'  => in_array(Ability::HOMEROOM, $user->access),
                        'is_headmaster'  => in_array(Ability::HEADMASTER, $user->access),
                        'is_ppdb_master'  => in_array(Ability::PPDB, $user->access),
                        'is_ppdb'  => in_array(Ability::PPDB, $user->access),
                        'is_counselor'  => in_array(Ability::COUNSELING, $user->access)
                    ]
                );
            }
        }

        return 0;
    }
}
