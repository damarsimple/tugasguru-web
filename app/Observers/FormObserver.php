<?php

namespace App\Observers;

use App\Enum\Constant;
use App\Models\Attachment;
use App\Models\Course;
use App\Models\Form;
use App\Models\Quiz;
use App\Models\School;
use App\Models\Subject;
use App\Models\Video;
use App\Notifications\FormApproved;
use App\Notifications\FormProcessed;
use App\Notifications\FormRejected;

class FormObserver
{
    /**
     * Handle the Form "created" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function created(Form $form)
    {
        //
    }

    /**
     * Handle the Form "updated" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function updated(Form $form)
    {
        switch ($form->status) {
            case Form::FINISHED:
                $form->user->notify(new FormApproved($form));
                $this->handleFinished($form);
                break;
            case Form::PROCESSED:
                $form->user->notify(new FormProcessed($form));
                break;
            case Form::REJECTED:
                $form->user->notify(new FormRejected($form));
                break;
        }
    }

    public function handleFinished(Form $form)
    {

        // business logic

        switch ($form->type) {
            case Form::REPORT_QUIZ:
                $user = $form->user;

                $quiz = Quiz::findOrFail($form->data->quiz->id);

                $quiz->delete();
                break;
            case Form::REQUEST_COUNSELOR:
                $user = $form->user;
                $user->is_counselor = true;
                $user->save();
                break;
            case Form::REQUEST_HOMEROOM:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_homeroom' => true]);
                $user->save();
                break;
            case Form::REQUEST_HEADMASTER:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_headmaster' => true]);
                $user->save();
                break;
            case Form::REQUEST_ADMIN_SCHOOL:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_administrator' => true]);
                break;
            case Form::REQUEST_TUTOR:
                $user = $form->user;
                $user->is_bimbel = true;
                $user->save();
                break;
            case Form::REQUEST_ADD_SUBJECT:
                $subject =  new Subject();
                $subject->name = $form->data->name;
                $subject->type = $form->data->type;

                $subject->save();
                break;
            case Form::COURSE_CREATE_REQUEST:
                $data = $form->data;
                $course = new Course();
                $course->subject_id = $data->subject;
                $course->classtype_id = $data->classtype;

                $course->name = $data->name;
                $course->description = $data->description;

                $form->user->courses()->save($course);

                foreach ($data->videos as $videoData) {
                    $video = new Video();

                    $video->description = $videoData->description ?? "";

                    $video->name = $videoData->name;

                    $course->videos()->save($video);

                    $attachment = Attachment::findOrFail($videoData->file->id);

                    $attachment->role = Constant::VIDEO;

                    $attachment->attachable_id = $video->id;

                    $attachment->attachable_type = $video::class;

                    $attachment->save();
                }
                break;
        }
    }

    /**
     * Handle the Form "deleted" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function deleted(Form $form)
    {
        //
    }

    /**
     * Handle the Form "restored" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function restored(Form $form)
    {
        //
    }

    /**
     * Handle the Form "force deleted" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function forceDeleted(Form $form)
    {
        //
    }
}
