<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Assigment;
use App\Models\Attachment;
use App\Models\Attendance;
use App\Models\Autosave;
use App\Models\Booking;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Form;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Quiz;
use App\Models\School;
use App\Models\StudentAssigment;
use App\Models\StudentPpdb;
use App\Models\Subject;
use App\Models\Transaction;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\AssigmentObserver;
use App\Observers\AttachmentObserver;
use App\Observers\AttendanceObserver;
use App\Observers\AutosaveObserver;
use App\Observers\BookingObserver;
use App\Observers\ConsultationObserver;
use App\Observers\ExamObserver;
use App\Observers\FormObserver;
use App\Observers\MeetingObserver;
use App\Observers\MessageObserver;
use App\Observers\QuizObserver;
use App\Observers\SchoolObserver;
use App\Observers\StudentAssigmentObserver;
use App\Observers\StudentPpdbObserver;
use App\Observers\SubjectObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Article::observe(ArticleObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Message::observe(MessageObserver::class);
        Meeting::observe(MeetingObserver::class);
        Attachment::observe(AttachmentObserver::class);
        StudentAssigment::observe(StudentAssigmentObserver::class);
        Consultation::observe(ConsultationObserver::class);
        Form::observe(FormObserver::class);
        Exam::observe(ExamObserver::class);
        Transaction::observe(TransactionObserver::class);
        Quiz::observe(QuizObserver::class);
        Assigment::observe(AssigmentObserver::class);
        Attendance::observe(AttendanceObserver::class);
        StudentPpdb::observe(StudentPpdbObserver::class);
        School::observe(SchoolObserver::class);
        User::observe(UserObserver::class);
        Subject::observe(SubjectObserver::class);
        Autosave::observe(AutosaveObserver::class);
        Booking::observe(BookingObserver::class);
    }
}
