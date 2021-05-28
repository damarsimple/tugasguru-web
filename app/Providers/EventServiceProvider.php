<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Form;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\StudentAssigment;
use App\Models\Transaction;
use App\Observers\ArticleObserver;
use App\Observers\AttachmentObserver;
use App\Observers\ConsultationObserver;
use App\Observers\ExamObserver;
use App\Observers\FormObserver;
use App\Observers\MeetingObserver;
use App\Observers\MessageObserver;
use App\Observers\StudentAssigmentObserver;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
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
    }
}
