<?php

namespace App\Observers;

use App\Events\TransactionEvent;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Notifications\TransactionFailed;
use App\Notifications\TransactionPending;
use App\Notifications\TransactionSuccess;

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
            $transaction->payment_method == Transaction::BALANCE
            && $transaction->is_paid
            && $transaction->status == Transaction::SUCCESS
        ) {
            $transaction->from = $transaction->user->balance;
            $transaction->to = $transaction->user->balance - $transaction->amount;
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

    public function handlePaid(Transaction $transaction)
    {
        switch ($transaction->transactionable_type) {
            case 'App\Models\Subscription':
                $subscription = $transaction->transactionable;

                $transaction->user->subscriptions()->attach(
                    $subscription,
                    ['expired_at' =>
                    now()->addDay($subscription->duration)]
                );

                break;

            default:
                break;
        }

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
            $transaction->payment_method == Transaction::XENDIT && $transaction->is_paid && ($transaction->status == Transaction::SUCCESS || $transaction->status == Transaction::STAGING)
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
