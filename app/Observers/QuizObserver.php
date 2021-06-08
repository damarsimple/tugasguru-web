<?php

namespace App\Observers;

use App\Models\Quiz;
use App\Models\QuizReward;
use App\Models\Reward;
use App\Models\Transaction;
use App\Notifications\TransactionSuccess;
use Illuminate\Support\Str;

class QuizObserver
{
    /**
     * Handle the Quiz "created" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function created(Quiz $quiz)
    {
        //
    }

    /**
     * Handle the Quiz "updated" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function updated(Quiz $quiz)
    {
        if ($quiz->is_rewarded) {
            return;
        }

        $reward = Reward::where("is_active", true)->first();

        if (!$reward) {
            return;
        }

        $user = $quiz->user;

        if ($quiz->played_count >= $reward->minimum_play_count) {
            $quiz->is_rewarded = true;
            $quiz->saveQuietly();

            $quizreward = new QuizReward();
            $quizreward->quiz_id = $quiz->id;
            $quizreward->reward_id = $reward->id;
            $quizreward->reward = $reward->reward;
            $quizreward->save();

            $transaction = new Transaction();
            $transaction->payment_method = Transaction::ADMIN;
            $transaction->uuid = Str::uuid();
            $transaction->amount = $quizreward->reward;
            $transaction->from = $user->balance;
            $transaction->to = $user->balance + $quizreward->reward;
            $transaction->description =
                "Hadiah dari " .
                $quiz->name .
                " sebesar " .
                $transaction->amount;
            $transaction->is_paid = true;
            $transaction->user_id = $user->id;
            $transaction->status = Transaction::SUCCESS;
            $quizreward->transaction()->save($transaction);

            $user->balance += $quizreward->reward;
            $user->save();

            $user->notify(new TransactionSuccess($transaction));

            // deactive reward when prizepool reaches zero automatically
            $reward->prize_pool = $reward->prize_pool - $reward->reward;
            $reward->is_active = $reward->prize_pool > 0;
            $reward->save();
        }
    }

    /**
     * Handle the Quiz "deleted" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function deleted(Quiz $quiz)
    {
        //
    }

    /**
     * Handle the Quiz "restored" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function restored(Quiz $quiz)
    {
        //
    }

    /**
     * Handle the Quiz "force deleted" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function forceDeleted(Quiz $quiz)
    {
        //
    }
}
