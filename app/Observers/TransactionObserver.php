<?php

namespace App\Observers;

use App\Enum\Ability;
use App\Events\TransactionEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionFailed;
use App\Notifications\TransactionPending;
use App\Notifications\TransactionSuccess;
use Illuminate\Support\Str;


class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {

        if (
            $transaction->payment_method == Transaction::BALANCE &&
            $transaction->is_paid &&
            $transaction->status == Transaction::SUCCESS
        ) {
            $transaction->from = $transaction->user->balance;
            $transaction->to =
                $transaction->user->balance - $transaction->amount;
            $transaction->saveQuietly();
            $this->handlePaid($transaction);
        }

        broadcast(new TransactionEvent($transaction));
        $this->callNotification($transaction);
    }

    public function callNotification(Transaction $transaction)
    {
        $user = $transaction->user;

        switch ($transaction->status) {
            case Transaction::SUCCESS:
                $user->notify(new TransactionSuccess($transaction));
                break;
            case Transaction::PENDING:
                $user->notify(new TransactionPending($transaction));
                break;
            case Transaction::FAILED:
                $user->notify(new TransactionFailed($transaction));
                break;
        }
    }

    private function payAdmin(Transaction $transaction)
    {
        $admin = User::where('is_admin', true)->first();

        if (!$admin) return;

        $adminTransaction = new Transaction();

        $adminTransaction->uuid = Str::uuid();

        $adminTransaction->from = $admin->balance;

        $adminTransaction->to = $admin->balance + $transaction->amount;

        $adminTransaction->payment_method  = Transaction::BALANCE;

        $adminTransaction->transaction_id = $transaction->id;

        $adminTransaction->transactionable_id = $transaction->transactionable_id;

        $adminTransaction->transactionable_type = $transaction->transactionable_type;

        $adminTransaction->amount = $transaction->amount;

        $adminTransaction->description =
            $transaction->description;

        $adminTransaction->is_paid = true;

        $adminTransaction->status = Transaction::SUCCESS;

        $admin->transactions()->save($adminTransaction);
    }


    private function handlePaid(Transaction $transaction)
    {
        $user = $transaction->user;

        $abilities = $user->access ?? [];

        switch ($transaction->transactionable_type) {
            case "App\Models\Access":
                $subscription = $transaction->transactionable;

                $user
                    ->accesses()
                    ->attach($subscription, [
                        "expired_at" => now()->addDay($subscription->duration),
                    ]);

                foreach ($subscription->ability as $ability) {
                    $abilities[] = $ability;
                }

                $this->payAdmin($transaction);

                break;
            case "App\Models\Booking":
                if ($transaction->transactionable->user_id == $transaction->user_id) {
                    $this->payAdmin($transaction);
                }
                break;
            default:
                break;
        }

        $abilities = array_unique($abilities);

        $user->access = $abilities;

        foreach ($user->schools as $school) {
            $school->teachers()->updateExistingPivot($user->id, [
                "is_homeroom" => in_array(Ability::HOMEROOM, $user->access),
                "is_headmaster" => in_array(Ability::HEADMASTER, $user->access),
                "is_administrator" => in_array(Ability::ADMIN_SCHOOL, $user->access),
                "is_counselor" => in_array(Ability::COUNSELING, $user->access),
            ]);
        }

        $user->save();

        $this->callNotification($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        if (
            $transaction->payment_method == Transaction::XENDIT &&
            $transaction->is_paid &&
            ($transaction->status == Transaction::SUCCESS ||
                $transaction->status == Transaction::STAGING)
        ) {
            $this->handlePaid($transaction);
        }

        broadcast(new TransactionEvent($transaction));
    }

    /**
     * Handle the Transaction "deleted" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }
}
