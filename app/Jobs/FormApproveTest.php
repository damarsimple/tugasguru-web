<?php

namespace App\Jobs;

use App\Models\Form;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FormApproveTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Form $form)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(7);
        $form = $this->form;
        $form->status = Form::FINISHED;
        switch ($form->type) {
            case Form::REQUEST_COUNSELOR:
                $user = $form->user;
                $user->is_counselor = true;
                $user->save();
                break;
            case Form::REQUEST_HOMEROOM:
                $user = $form->user;
                try {
                    $school = School::findOrFail($form->data->school);
                } catch (\Throwable $th) {
                    $school = School::findOrFail(json_decode($form->data)->school);
                }
                $school->teachers()->updateExistingPivot($user, ['is_homeroom' => true]);
                $user->save();
                break;
            case Form::REQUEST_HEADMASTER:
                $user = $form->user;
                try {
                    $school = School::findOrFail($form->data->school);
                } catch (\Throwable $th) {
                    $school = School::findOrFail(json_decode($form->data)->school);
                }
                $school->teachers()->updateExistingPivot($user, ['is_headmaster' => true]);
                $user->save();
                break;
            case Form::REQUEST_ADMIN_SCHOOL:
                $user = $form->user;
                try {
                    $school = School::findOrFail($form->data->school);
                } catch (\Throwable $th) {
                    $school = School::findOrFail(json_decode($form->data)->school);
                }
                $school->teachers()->updateExistingPivot($user, ['is_ppdb' => true]);
                break;
            case Form::REQUEST_TUTOR:
                $user = $form->user;
                $user->is_bimbel = true;
                $user->save();
                break;
        }
        $form->save();
    }
}
