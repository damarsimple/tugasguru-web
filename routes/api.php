<?php

use App\Actions\Attachment\Upload;
use App\Enum\Constant;
use App\Events\MeetingChangeEvent;
use App\Events\QuizRoomChangeEvent;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureGradeReport;
use App\Http\Middleware\EnsureHeadmaster;
use App\Http\Middleware\EnsureHomeroom;
use App\Http\Middleware\EnsureStudent;
use App\Http\Middleware\EnsureTeacher;
use App\Http\Middleware\EnsureXendit;
use App\Http\Middleware\EnsureAdminSchool;
use App\Http\Middleware\EnsureGuardian;
use App\Http\Middleware\EnsureGuardianPaid;
use App\Http\Middleware\EnsureStudentPPDB;
use App\Jobs\FormApproveTest;
use App\Jobs\SendVerifyEmailJob;
use App\Misc\AppConfig;
use App\Models\Absent;
use App\Models\Access;
use App\Models\Agenda;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Assigment;
use App\Models\Attachment;
use App\Models\Attendance;
use App\Models\Autosave;
use App\Models\Booking;
use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\Comment;
use App\Models\Consultation;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Examresult;
use App\Models\Examsession;
use App\Models\Examtracker;
use App\Models\Examtype;
use App\Models\Extracurricular;
use App\Models\Form;
use App\Models\FormTemplate;
use App\Models\Like;
use App\Models\Major;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Packagequestion;
use App\Models\Price;
use App\Models\PrivateRoom;
use App\Models\Province;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\Quizresult;
use App\Models\Report;
use App\Models\Room;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\StudentAnswer;
use App\Models\StudentAssigment;
use App\Models\StudentPpdb;
use App\Models\Subject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Video;
use App\Models\Voucher;
use App\Models\Wave;
use App\Notifications\QuizInvite;
use App\Payment\Xendit;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Octane\Facades\Octane;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);

Route::get('me', fn (Request $request) => $request->user());

Route::group(['prefix' => 'attachments'], function () {
    Route::post('/temp', function (Request $request) {
        $files = $request->file('file');
        $isProcessed = (bool) $request->get('is_proccessed') ?? false;
        $originalSize = (int) $request->get('original_size') ?? false;
        $compressedSize = (int) $request->get('compressed_size') ?? false;
        $attachment =  Upload::handle($files, $isProcessed, $originalSize, $compressedSize, false);

        return $attachment;
    });
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:sanctum')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['throttle:6,1'])
    ->name('verification.verify');


Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? ['status' => $status]
        : ['email' => $status];
})->name('password.email');


Route::get('/reset-password/{token}', function (Request $request, $token) {
    return redirect(env('SPA_URL') . "/forgot-token/$token?email=" . $request->email);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? ['status' => $status]
        : ['email' => [__($status)]];
})->name('password.update');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::middleware('web')->get('auth/google/callback', function () {

    $data =  Socialite::driver('google')?->stateless()?->user();

    $user = User::where('email', $data?->email)->first();

    if (!$user) {
        return redirect(
            env('FRONT_END_REGISTER_URL') .
                '/?email=' . $data?->email
                . '&name=' . $data?->name
                . '&avatar=' . $data?->avatar_original
        );
    } else {
        $token = $user->createToken('google-' . $user->name)->plainTextToken;
        return redirect(
            env('FRONT_END_CALLBACK') .
                '/?token=' . $token
                . '&socialToken=' . $data?->token . '&social=google'
        );
    }
});
Route::middleware('web')->get('social', function () {
    return Socialite::driver('google')->redirect();
});
Route::get('examtypes', fn () => Examtype::all());
Route::get('/provinces', fn () => Province::all());
Route::get('/schooltypes', fn () => Schooltype::all());
Route::get('/subjects', fn () => Subject::all());
Route::get('/classtypes', fn () => Classtype::all());
Route::get('/provinces/{id}/city', fn ($id) => Province::findOrFail($id)->cities);
Route::get('/cities/{id}/districts', fn ($id) => City::findOrFail($id)->districts);
Route::get('/cities/{id}/schools', function (Request $request, $id) {
    $city =  City::findOrFail($id);

    if ($request->q) {
        return $city->schools()
            ->where('name', 'LIKE', "%$request->q%")
            ->orWhere('npsn', 'LIKE', "%$request->q%")
            ->take(10)
            ->get();
    } else {
        return $city->schools()
            ->take(10)
            ->get();
    }
});


Route::post("/token", [ApiAuthController::class, "token"]);
Route::post("/login", [ApiAuthController::class, "login"]);
Route::post("/register", [ApiAuthController::class, "register"]);

Route::middleware('auth:sanctum')->get("/user", [ApiAuthController::class, 'profile']);
Route::middleware('auth:sanctum')->get("/refresh", [ApiAuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->get('attendances/{uuid}', function ($uuid) {
    $attendance = Attendance::where('uuid', $uuid)->firstOrFail();

    $attendance->attended = true;

    $attendance->save();

    return 'ok';
});

Route::middleware('auth:sanctum')->get("/agenda/{uuid}", function (Request $request, $uuid) {
    $agenda = Agenda::where('uuid', $uuid)->firstOrFail();

    $attendance = $agenda->attendances()->where('user_id', $request->user()->id)->firstOrFail();

    $attendance->attended = true;

    $attendance->save();

    return 'ok';
});


Route::group(['middleware' => ['auth:sanctum', EnsureAdmin::class], 'prefix' => 'admins'], function () {
    Route::put('/forms/{id}', function (Request $request, $id) {
        $form = Form::findOrFail($id);

        $form->comment = $request->comment;

        $form->status = $request->status;

        if ($request->data) {
            $form->data = $request->data;
        }

        $form->save();
    });
    Route::put('/config/{key}', function (Request $request, $key) {
        $appConfig = new AppConfig;

        $appConfig->set($key, $request->value);

        return 'OK';
    });

    Route::get('/config/{key}', function (Request $request, $key) {
        $appConfig = new AppConfig;

        return $appConfig->get($key);
    });

    Route::group(['prefix' => 'schools'], function () {
        Route::post('/', function (Request $request) {
            $school = new School();

            $school->name = $request->name;
            $school->npsn = $request->npsn;

            $school->province_id = $request->province_id;
            $school->city_id = $request->city_id;
            $school->district_id = $request->district_id;
            $school->schooltype_id = $request->schooltype_id;

            $school->save();

            return response('OK', 200);
        });
        Route::post('/{id}', function (Request $request, $id) {
            $school = School::findOrFail($id);

            $school->name = $request->name;
            $school->npsn = $request->npsn;

            $school->province_id = $request->province_id ?? $school->province_id;
            $school->city_id = $request->city_id ?? $school->city_id;
            $school->district_id = $request->district_id ?? $school->district_id;
            $school->schooltype_id = $request->schooltype_id ?? $school->schooltype_id;
            $school->address = $request->address;

            $school->address = $request->address;


            $school->save();

            return response('OK', 200);
        });
    });
});


Route::group(['middleware' => [EnsureXendit::class], 'prefix' => 'xendit'], function () {
    Route::post('/invoices/paid', function (Request $request) {

        if ($request->external_id == Constant::XENDIT_TEST_EXTERNAL_UUID) {
            return response('OK', 200);
        }

        $transaction = Transaction::where('uuid', $request->external_id)->firstOrFail();

        if ($request->status == "PAID" || $request->status == "SETTLED") {
            $transaction->is_paid = true;
            $transaction->status = Transaction::SUCCESS;
            $transaction->invoice_response = $request->toArray();

            $transaction->save();
        } else {
            $transaction->status = Transaction::FAILED;
            $transaction->invoice_response = $request->toArray();

            $transaction->save();
        }

        return ['message' => 'validated', 'transaction' => $transaction];
    });
});

Route::group(['prefix' => 'guardians', 'middleware' => [EnsureGuardian::class, EnsureGuardianPaid::class]], function () {
    Route::group(['prefix' => 'consultations'], function () {
        Route::post('/', function (Request $request) {

            $user = $request->user()->childrens()->findOrFail($request->child);

            $constult = new Consultation();
            $constult->consultant_id = User::findOrFail($request->teacher)->id;
            $constult->school_id = $user->school_id;
            $constult->name = $request->name;
            $constult->problem = $request->problem;
            $user->consultations()->save($constult);
        });

        Route::get('/', fn (Request $request) => $request->user()->consultations()->paginate(10));
        Route::get('{id}', fn (Request $request, $id) => $request->user()->consultations()->findOrFail($id));
    });
    Route::group(
        ['prefix' => '/absents'],
        function () {
            Route::get('/', function (Request $request) {
                $child = $request->user()->childrens()->findOrFail($request->child);
                return $child->user()->absents()->paginate(10);
            });
            Route::post(
                '/',
                function (Request $request) {
                    $user = $request->user()->childrens()->findOrFail($request->child);

                    $startAt =  Carbon::parse($request->start_at);
                    $finishAt = Carbon::parse($request->finish_at);

                    $startAt->hour = now()->hour;
                    $startAt->minute = now()->minute;
                    $startAt->second = now()->second;

                    $finishAt->hour = $startAt->hour;
                    $finishAt->minute = $startAt->minute;
                    $finishAt->second = $startAt->second;

                    $absent = new Absent();
                    $absent->receiver_id = $request->teacher;
                    $absent->type = $request->type;
                    $absent->reason = $request->reason;
                    $absent->start_at = $startAt;
                    $absent->finish_at = $finishAt;

                    $user->absents()->save($absent);
                }
            );
        }
    );
    Route::get('/', fn (Request $request) => $request->user()->childrens);
    Route::group(
        ['prefix' => '/childrens'],
        function () {
            Route::post('admit', function (Request $request) {
                $child = User::where('nisn', $request->nisn)->firstOrFail();

                $user = $request->user();

                if ($child->phone != $request->phone) {
                    return response('Nomor Telepon Salah', 400);
                }

                $child->parent_id = $user->id;

                $child->save();

                return response('OK', 200);
            });
            Route::post('kick', function (Request $request) {
                $user = $request->user()->childrens()->where('id', $request->children)->firstOrFail();

                if (!$user) {
                    return response('Anak tidak ditemukan', 400);
                }

                $user->parent_id = null;
                $user->save();

                return response('OK', 200);
            });
        }
    );
    Route::group(
        ['prefix' => '/recap'],
        function () {
            Route::group(
                ['prefix' => '/grades'],
                function () {
                    Route::get('/', function (Request $request) {
                        $child = $request->user()->childrens()->findOrFail($request->children);
                        $examresultSubjects = $child->examresults()->get()->groupBy('exam.subject.name');
                        $studentassigments = $child->studentassigments()->get()->groupBy('assigment.subject.name');


                        $map = [];

                        foreach ($examresultSubjects as $subject => $examresult) {
                            $types = $examresult->groupBy('exam.examtype.name');

                            foreach ($types as $type => $value) {
                                $map[$subject][$type] = $value;
                            }
                        }
                        foreach ($studentassigments as $subject => $value) {
                            $map[$subject]["Tugas"] = $value;
                        }

                        return $map;
                    });
                }
            );
        }
    );
});
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'courses'], function () {
    Route::post('/', function (Request $request) {
        $user = $request->user();

        if ($user->is_admin) {
            $course = new Course();
            $course->subject_id = $request->subject;
            $course->classtype_id = $request->classtype;

            $course->name = $request->name;
            $course->description = $request->description;


            if ($request->as_id) {
                $user = User::findOrFail($request->as_id);
            }

            $user->courses()->save($course);


            foreach ($request->videos as $videoData) {
                $video = new Video();

                $video->description = $videoData['description'] ?? "";

                $video->name = $videoData['name'];

                $course->videos()->save($video);

                $attachment = Attachment::findOrFail($videoData['file']['id']);

                $attachment->role = Constant::VIDEO;

                $attachment->attachable_id = $video->id;

                $attachment->attachable_type = $video::class;

                $attachment->save();
            }


            if ($request->attachment) {
                $attachment = Attachment::findOrFail($request->attachment);
                $attachment->role = Constant::THUMBNAIL;
                $attachment->save();
                $course->thumbnail()->save($attachment);
            }
        } else {
            $form = new Form();

            $form->type = $request->type;

            $form->data = $request->toArray();

            $user->forms()->save($form);
        }
        return ['message' => 'ok'];
    });
    Route::get('/', function () {
        return Quiz::latest()->paginate(10);
    });
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'quiz'], function () {
    Route::post('/', function (Request $request) {

        $quiz = new Quiz();
        $quiz->subject_id = $request->subject;

        $quiz->name = $request->name;
        $quiz->description = $request->description;
        $quiz->visibility = $request->visibility;

        $user = $request->user();

        $user->quizzes()->save($quiz);

        $questionIds = [];

        foreach ($request->questions as $questionData) {
            if (!array_key_exists('id', $questionData)) {
                $question = new Question();

                $question->content = $questionData['content'];

                $question->visibility = $request['visibility'];

                $question->type = $questionData['type'];

                $question->subject_id = $request['subject'];

                $user->questions()->save($question);

                $questionIds[] = $question->id;
                $answers = [];
                foreach ($questionData['answers'] as $i => $answerData) {
                    $answer = new Answer();

                    if ($questionData['type'] !== 'MULTI_CHOICE') {
                        $answer->is_correct = true;
                    } else {
                        $answer->is_correct = $i == $questionData['correctanswer'];
                    }

                    $answer->content = $answerData['content'] ?? '';
                    $answers[] = $answer;
                }

                $question->answers()->saveMany($answers);

                if (array_key_exists('answerattachments', $questionData)) {
                    foreach ($questionData['answerattachments'] as $i => $attachment) {
                        if (!empty($attachment)) {
                            Attachment::find($attachment)->attachable()->associate($answers[$i])->save();
                        }
                    }
                }

                foreach (Attachment::whereIn('id', $questionData['attachments'])->get() as $attachment) {
                    $attachment->attachable()->associate($question)->save();
                }
            } else {
                $questionIds[] = $questionData['id'];
            }
        }


        $quiz->questions()->attach($questionIds);

        if ($request->attachment) {
            $attachment = Attachment::findOrFail($request->attachment);
            $attachment->role = Constant::THUMBNAIL;
            $attachment->save();
            $quiz->thumbnail()->save($attachment);
        }

        return ['message' => 'ok'];
    });

    Route::get('/', function () {
        return Quiz::latest()->paginate(10);
    });

    Route::get('{id}', function ($id) {
        return Quiz::with('questions.answers')->findOrFail($id);
    });

    Route::group(['prefix' => 'rooms'], function () {
        Route::post('/create', function (Request $request) {
            $user = $request->user();

            $quiz = Quiz::findOrFail($request->quiz);

            $room = new Room();

            $room->name = $request->name;

            $room->identifier = Str::uuid();
            $room->is_enabled = false;

            $quiz->rooms()->save($room);

            $room->users()->attach($user->id, ['is_administrator' => true]);

            broadcast(new QuizRoomChangeEvent($room));

            return Room::with('users')->find($room->id);
        });
        Route::post('/join', function (Request $request) {
            $room = Room::where('identifier', $request->identifier)->firstOrFail();
            $room->users()->attach($request->user()->id);
            broadcast(new QuizRoomChangeEvent($room));
            return [
                'room' => Room::with('users')->find($room->id),
                'quiz' => Quiz::with('questions.answers')->findOrFail($room->roomable_id)
            ];
        });
        Route::post('/invite', function (Request $request) {
            $room = Room::where('identifier', $request->identifier)->firstOrFail();
            $user = User::where('id', $request->user)->firstOrFail();
            $user->notify(new QuizInvite($room));
            return 'ok';
        });
        Route::put('{id}', function (Request $request, $id) {
            $room = Room::find($id);
            $room->is_enabled = $request->is_enabled;
            if ($room->is_enabled) {
                $quiz = Quiz::findOrFail($room->roomable_id);
                $quiz->increment('played_count');
                foreach ($room->users as $user) {
                    $quizresult  = new Quizresult();
                    $quizresult->user_id = $user->id;
                    $quizresult->quiz_id = $room->roomable_id;
                    $room->quizresults()->save($quizresult);
                }
            }
            $room->save();

            broadcast(new QuizRoomChangeEvent($room));
        });
        Route::post('/answer', function (Request $request) {
            $user = $request->user();

            $quizresult = Quizresult::firstOrCreate([
                'quiz_id' => $request->quiz,
                'room_id' => $request->room,
                'user_id' => $user->id,
            ]);

            $quizanswer  = QuizAnswer::firstOrCreate([
                'question_id' => $request->question,
                'user_id' => $user->id,
                'quiz_id' => $request->quiz,
                'room_id' => $request->room,
                'quizresult_id' => $quizresult->id
            ]);

            $quizanswer->answer_id = $request->answer_id;
            $quizanswer->content = $request->content;
            $quizanswer->grade = $request->grade;
            $quizanswer->is_correct = $request->is_correct;

            $quizresult->grade = $request->accumulateGrade;

            $quizresult->save();

            $quizanswer->save();

            broadcast(new QuizRoomChangeEvent(Room::findOrFail($request->room)));
            return 'ok';
        });
    });
});


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'payments'], function () {
    Route::post('/accesses', function (Request $request) {
        $user = $request->user();

        $subscription = Access::findOrFail($request->subscription);

        $transaction = new Transaction();

        if ($request->voucher) {
            $voucher = Voucher::where(['code' => $request->voucher])->firstOrFail();

            if (!$voucher->expired_at->lt(now())) {
                $transaction->amount = ($subscription->price - $subscription->price * $voucher->percentage);
                $transaction->voucher_id = $voucher->id;
            } else {
                return response([
                    'message' => 'Voucher sudah kadaluarsa',
                ], 403);
            }
        } else {
            $transaction->amount = $subscription->price;
        }

        $transaction->from = $user->balance;
        $transaction->to = $user->balance;

        $transaction->payment_method = $request->payment_method;

        $transaction->transactionable_id = $subscription->id;
        $transaction->transactionable_type = $subscription::class;

        $transaction->description = 'Pembelian ' . $subscription->name . ' sebesar ' . $transaction->amount;
        $transaction->uuid = Str::uuid();

        switch ($request->payment_method) {
            case 'XENDIT':

                $invoice = Xendit::makePayment(
                    amount: $transaction->amount,
                    uuid: $transaction->uuid,
                    description: $transaction->description,
                    email: $user->email
                );

                $transaction->invoice_request = $invoice;

                $transaction->staging_url = $invoice['invoice_url'];

                break;
            case 'BALANCE':
                if ($user->balance < $transaction->amount) {
                    return ['message' => 'Uang anda tidak cukup'];
                }

                $transaction->is_paid = true;
                $transaction->status = Transaction::SUCCESS;

                break;
            default:
                return ['message' => 'Metode pembayaran invalid'];
                break;
        }

        $user->transactions()->save($transaction);

        return [
            'message' => 'ok',
            'transaction' => $transaction,
            'staging_url' => $transaction->staging_url
        ];
    });
    Route::post('/ppdbs', function (Request $request) {
        $user = $request->user();

        $wave = $user?->studentppdb?->wave;

        if (!$wave) {
            return ['message' => 'you didnt join wave yet'];
        }

        $transaction = new Transaction();

        if ($request->voucher) {
            $voucher = Voucher::where(['code' => $request->voucher])->firstOrFail();

            if (!$voucher->expired_at->lt(now())) {
                $transaction->amount = ($wave->price - $wave->price * $voucher->percentage);
                $transaction->voucher_id = $voucher->id;
            } else {
                return response([
                    'message' => 'Voucher sudah kadaluarsa',
                ], 403);
            }
        } else {
            $transaction->amount = $wave->price;
        }

        $transaction->from = $user->balance;
        $transaction->to = $user->balance;

        $transaction->payment_method = $request->payment_method;

        $transaction->transactionable_id = $wave->id;
        $transaction->transactionable_type = $wave::class;

        $transaction->description = 'Pembayarn ' . $wave->name . ' sebesar ' . $transaction->amount;

        $transaction->staging_url = null;
        $transaction->uuid = Str::uuid();

        switch ($request->payment_method) {
            case 'XENDIT':

                $invoice = Xendit::makePayment(
                    amount: $transaction->amount,
                    uuid: $transaction->uuid,
                    description: $transaction->description,
                    email: $user->email
                );

                $transaction->invoice_request = $invoice;

                $transaction->staging_url = $invoice['invoice_url'];

                break;
            default:
                return ['message' => 'Metode pembayaran invalid'];
                break;
        }

        $user->transactions()->save($transaction);

        return [
            'message' => 'ok',
            'transaction' => $transaction,
            'staging_url' => $transaction->staging_url
        ];
    });
});
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'meetings'], function () {
    Route::get('/{id}', function (Request $request, $id) {
        $user = $request->user();

        if ($user->roles == "TEACHER") {
            return $user->meetings()->with('classroom.students')->findOrFail($id);
        } else {
            $meeting = Meeting::with('classroom.students')->with(['rooms' => function ($e) use ($user) {
                return $e->whereHas('users', fn ($e) => $e->where('user_id', $user->id));
            }])->findOrFail($id);



            $attendance = Attendance::firstOrCreate([
                'school_id' => $meeting->classroom->school_id,
                'agenda_id' => $meeting->agenda->id,
                'user_id' => $user->id,
            ]);

            $attendance->attended = true;
            $attendance->save();

            return $meeting;
        }
        return;
    });
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'rooms'], function () {
    Route::get('/{id}', function (Request $request, $id) {
        $user = $request->user();
        return  $user->rooms()->find($id)->messages;
    });
    Route::post('/{id}', function (Request $request, $id) {
        $sender = $request->user();

        $message = new Message();

        $message->content = $request->content;

        $message->user_id = $sender->id;

        $room = Room::findOrFail($id);

        // if (!in_array($sender->id, [$room->first_id, $room->second_id])) {
        //     return ['message' => 'unauthorized'];
        // }

        $room->updated_at = now();

        $room->save();

        $room->messages()->save($message);

        return ['message' => 'ok'];
    });
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'users'], function () {
    Route::post('/togglebimbel', function (Request $request) {
        $user = auth()->user();

        $user->is_bimbel_active = $request->is_bimbel_active;

        $user->save();

        return $user;
    });
    Route::post('/reverify', function (Request $request) {
        try {
            SendVerifyEmailJob::dispatch($request->user());
            return response('OK', 200);
        } catch (\Throwable $th) {
            return response('Mail Server sedang Overload !', 500);
        }
    });
    Route::group(['prefix' => 'forms'], function () {

        Route::post('/', function (Request $request) {
            $form = new Form();

            $form->type = $request->type;

            $form->data = $request->data;

            $form->school_id = $request->school;

            $user = $request->user();

            $user->forms()->save($form);

            if ($request->attachment) {
                $attachment = Attachment::findOrFail($request->attachment);
                $attachment->description = $request->description;
                $attachment->role = $request->role;
                $attachment->attachable_id = $form->id;
                $attachment->attachable_type = Form::class;
                $attachment->save();
            }

            // if (config('app.env') == 'local' || config('app.debug') == true) {
            //     dispatch(new FormApproveTest($form));
            // }

            return ['message' => 'ok'];
        });
    });

    Route::group(['prefix' => 'bookings'], function () {
        Route::post('/', function (Request $request) {
            $user = $request->user();
            $basePrice =  (new AppConfig)->get(Constant::BOOKING_BIMBEL_BASE_PRICE);
            $teacher = User::findOrFail($request->teacher);

            $booking = new Booking();

            $booking->teacher_id = $request->teacher;
            $booking->start_at = Carbon::parse($request->start);
            $booking->finish_at = Carbon::parse($request->finish);
            $booking->address = $request->address;
            $booking->notes = $request->notes;

            $booking->coordinate = $request->coordinate;
            $booking->address_detail = $request->address_detail;

            $booking->status = Booking::MENUNGGU;
            $booking->rate = $basePrice;

            $user->bookings()->save($booking);

            $transaction = new Transaction();

            $daysDiff = $booking->start_at->diffInDays($booking->finish_at);


            $transaction->amount = ($daysDiff == 0 ? 1 : $daysDiff) * $basePrice;

            if ($transaction->amount == 1) {
                $transaction->amount = $basePrice;
            }

            $transaction->from = $user->balance;
            $transaction->to = $user->balance;

            $transaction->payment_method = 'XENDIT';

            $transaction->transactionable_id = $booking->id;
            $transaction->transactionable_type = $booking::class;

            $transaction->description = 'Pembayaran Guru Bimbel ' . $teacher->name . ' sebesar ' . $transaction->amount;

            $transaction->staging_url = null;
            $transaction->uuid = Str::uuid();

            switch ('XENDIT') {
                case 'XENDIT':

                    $invoice = Xendit::makePayment(
                        amount: $transaction->amount,
                        uuid: $transaction->uuid,
                        description: $transaction->description,
                        email: $user->email
                    );

                    $transaction->invoice_request = $invoice;

                    $transaction->staging_url = $invoice['invoice_url'];

                    break;
                case 'BALANCE':
                    if ($user->balance < $transaction->amount) {
                        return ['message' => 'Uang anda tidak cukup'];
                    }

                    $transaction->is_paid = true;
                    $transaction->status = Transaction::SUCCESS;

                    break;
                default:
                    return ['message' => 'Metode pembayaran invalid'];
                    break;
            }

            $user->transactions()->save($transaction);


            return [
                'message' => 'ok',
                'transaction' => $transaction,
                'staging_url' => $transaction->staging_url
            ];
        });
        Route::put('{id}', function (Request $request, $id) {
            $booking = Booking::findOrFail($id);

            $booking->is_approved = $request->is_approved;

            $booking->save();

            return ['message' => 'ok'];
        });
    });
    Route::group(['prefix' => 'attachments'], function () {
        Route::post('/temp', function (Request $request) {
            $files = $request->file('file');
            $isProcessed = (bool) $request->get('is_proccessed') ?? false;
            $originalSize = (int) $request->get('original_size') ?? false;
            $compressedSize = (int) $request->get('compressed_size') ?? false;
            $attachment =  Upload::handle($files, $isProcessed, $originalSize, $compressedSize, true);
            return $attachment;
        });
    });
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', function (Request $request) {
            return  $request->user()->transactions()->paginate(10);
        });
    });
    Route::group(['prefix' => 'comments'], function () {
        Route::post('/', function (Request $request) {
            $comment = new Comment();

            $comment->commentable_id = $request->commentable_id;
            $comment->commentable_type = $request->commentable_type;

            $comment->content = $request->content;

            $request->user()->comments()->save($comment);

            return response('OK', 200);
        });
    });
    Route::group(['prefix' => 'likes'], function () {
        Route::post('/', function (Request $request) {
            $check = $request->user()->likes()
                ->where('likeable_id', $request->likeable_id)
                ->where('likeable_type', $request->likeable_type);

            if ($check->exists()) {
                $check->delete();
                $check = false;
            } else {
                $like = new Like();

                $like->likeable_id = $request->likeable_id;
                $like->likeable_type = $request->likeable_type;

                $request->user()->likes()->save($like);
                $check = true;
            }


            return response($check, 200);
        });
    });
    Route::group(['prefix' => 'messages'], function () {
        Route::delete('/{id}', function (Request $request, $id) {
            $message = Message::findOrFail($id);

            if (!$request->user()->id == $message->user_id) {
                return response('no', 401);
            }

            $message->delete();

            return response('ok', 200);
        });
    });
    Route::group(['prefix' => 'attendances'], function () {
        Route::get('{id}', function (Request $request, $id) {
            return  $request->user()->attendances()->with('agenda')->findOrFail($id);
        });
    });
    Route::group(['prefix' => 'followers'], function () {
        Route::get('/following', function (Request $request) {
            $user = $request->user();

            return $user->followings;
        });

        Route::get('/myfollowing', function (Request $request) {
            return $request->user()->followings;
        });

        Route::post('/accept', function (Request $request) {
            $user = $request->user();

            $user->requestfollowers()->whereIn('follower_id', $request->acceptIds)->update(['is_accepted' => true]);

            return ['message' => 'ok'];
        });


        Route::get('/myfollower', function (Request $request) {
            return $request->user()->followers;
        });

        Route::post('/deny', function (Request $request) {
            $request->user()->requestfollowers()->detach($request->userId);

            return ['message' => 'ok'];
        });

        Route::post('/unfollow', function (Request $request) {
            $student = $request->user();

            $student->followings()->detach($request->user);

            return ['message' => 'ok'];
        });


        Route::post('/request', function (Request $request) {
            $user = $request->user();

            if ($user->id == $request->user) {
                return ['message' => 'Anda tidak bisa mengikuti diri sendiri'];
            }

            $candidate = User::findOrFail($request->user);


            if ($user->roles == User::TEACHER && $candidate->roles !== "TEACHER") {
                return ['message' => 'invalid follow target'];
            } elseif (
                $candidate->roles == "TEACHER" &&
                !$user->school->teachers->pluck('id')->contains($request->user)
            ) {
                return ['message' => 'Guru tidak ada di sekolah'];
            } else {
                $candidate->requestfollowers()->syncWithoutDetaching([$user->id]);
            }




            return ['message' => 'ok'];
        });

        Route::get('/myrequests', function (Request $request) {
            $user = $request->user();
            return $user->requestfollowers()->with('province')->get();
        });
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/{id}', function (Request $request, $id) {
            if ($id == 0) {
                return null;
            }
            return $request->user()->transactions()->findOrFail($id);
        });
    });


    Route::group(['prefix' => 'accesses'], function () {
        Route::get('/', function () {
            return Access::all();
        });

        Route::get('/my', function (Request $request) {
            return $request->user()->accesses;
        });
    });

    Route::get('mark-read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();

        return ['message' => 'ok'];
    });

    Route::get('notifications', function (Request $request) {
        return $request->user()->notifications;
    });


    Route::put('/', function (Request $request) {
        $user = $request->user();

        $user->name = $request->name ?? $user->name;
        $user->phone = $request->phone ?? $user->phone;
        $user->email = $request->email ?? $user->email;
        $user->dob = Carbon::parse($request->dob);

        $user->address = $request->address  ?? $user->address;

        $user->specialty = $request->specialty ?? $user->specialty;
        $user->academic_degree = $request->academic_degree ?? $user->academic_degree;

        if ($request->hidden_attribute) {
            $user->hidden_attribute = $request->hidden_attribute;
        }

        $user->save();

        if ($request->has('profilepicture')) {
            $newPicture = Attachment::findOrFail($request->profilepicture);
            $newPicture->role = User::PROFILEPICTURE;
            $newPicture->save();
            $user->profilepicture()->delete();
            $user->profilepicture()->save($newPicture);
        }

        return ['message' => 'ok'];
    });

    Route::group(['prefix' => 'messages'], function () {
        Route::group(['prefix' => 'private'], function () {
            Route::get('/targets', function (Request $request) {
                $user = $request->user();

                if ($user->roles == User::TEACHER) {
                    $y = $user->schools()->get();
                    $users = [];
                    foreach ($y as $school) {
                        foreach ($school->teachers as $teacher) {
                            $users[] = $teacher;
                        }
                        foreach ($school->students as $student) {
                            $users[] = $student;
                        }
                    }
                    return array_merge($users, $user->followers->toArray());
                } elseif ($user->roles == User::STUDENT) {
                    $y = $user->school;
                    return array_merge($y->teachers->toArray(), $y->students->toArray(), $user->followers->toArray());
                } else {
                    return $user->followers;
                }
            });
            Route::get('rooms', function (Request $request) {
                $sender = $request->user();

                return PrivateRoom::where('first_id', $sender->id)->orWhere('second_id', $sender->id)->latest('updated_at')->get();
            });
            Route::get('rooms/{id}', function (Request $request, $id) {
                if ($id == 'undefined') {
                    return ['message' => 'ok'];
                }

                $sender = $request->user();

                $privateroom = PrivateRoom::with('messages')->findOrFail($id);

                if (!in_array($sender->id, [$privateroom->first_id, $privateroom->second_id])) {
                    return ['message' => 'unauthorized'];
                }

                return $privateroom;
            });

            Route::post('rooms/{id}', function (Request $request, $id) {
                $sender = $request->user();

                $message = new Message();

                $message->content = $request->content;

                $message->user_id = $sender->id;

                $privateroom = PrivateRoom::findOrFail($id);

                if (!in_array($sender->id, [$privateroom->first_id, $privateroom->second_id])) {
                    return ['message' => 'unauthorized'];
                }

                $privateroom->updated_at = now();

                $privateroom->save();

                $privateroom->messages()->save($message);

                return ['message' => 'ok'];
            });

            Route::post('create', function (Request $request) {
                $sender = $request->user();

                $participants = [$sender->id, $request->receiver];

                sort($participants);

                [$first, $second] = $participants;
                $room = PrivateRoom::firstOrCreate([
                    'first_id' => $first,

                    'second_id' => $second
                ]);

                $room->first = User::find($first);
                $room->second = User::find($second);

                return  $room;
            });
        });
    });



    Route::get('{id}', function (Request $request, $id) {
        return User::with(
            'followers',
            'frontarticles'
        )->findOrFail($id);
    });
});
Route::get('/attachments/{id}', fn ($id) => Attachment::findOrFail($id));

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'articles'], function () {
    Route::get('{slug}', fn ($slug) => Article::where('slug', $slug)->firstOrFail());
});


Route::get('/{id}/rank', function (Request $request, $id) {
    return Examresult::where('exam_id', $id)
        ->orderBy('grade', 'DESC')
        ->get();
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'students'], function () {
    Route::group(['prefix' => 'ppdb', 'middleware' => [EnsureStudentPPDB::class]], function () {
        Route::group(['prefix' => 'forms'], function () {
            Route::put('save', function (Request $request) {
                $user = $request->user();
                $studentppdb = $user->studentppdbs()->where('id', $request->id)->firstOrFail();
                $form = $studentppdb->form;

                $form->data = $request->data;

                if ($request->locked) {
                    $form->is_locked = true;
                }

                $form->save();

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'waves'], function () {
            Route::post('join', function (Request $request) {
                $wave = Wave::findOrFail($request->wave);

                $user = $request->user();

                $studentppdb = $user->studentppdbs()->where('school_id', $wave->school_id)->firstOrFail();

                $studentppdb->wave_id = $wave->id;

                if (!now()->between($wave->open_at, $wave->close_at)) {
                    return ['message' => 'Gelombang telah berakhir atau belum dibuka'];
                }

                if ($wave->max_join <  $wave->studentppdbs()->count()) {
                    return ['message' => 'Gelombang ini sudah penuh'];
                }

                $studentppdb->save();

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'schools'], function () {
            Route::post('join', function (Request $request) {
                $user = $request->user();

                $school = School::findOrFail($request->school);

                if (!$school->openWaves()->exists()) {
                    return response(['message' => 'Sekolah tidak memiliki gelombang terbuka'], 403);
                }

                $studentppdb = new StudentPpdb();

                $studentppdb->school_id = $school->id;

                $user->studentppdbs()->save($studentppdb);

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'majors'], function () {
            Route::post('join', function (Request $request) {
                $user = $request->user();

                $studentppdb = $user->studentppdbs()->where('id', $request?->id)->firstOrFail();

                $major = Major::findOrFail($request->major);

                $studentppdb->major_id = $major->id;

                $studentppdb->save();

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'extracurriculars'], function () {
            Route::post('join', function (Request $request) {
                $user = $request->user();

                $studentppdb = $user->studentppdbs()->where('id', $request?->id)->firstOrFail();

                if (!$studentppdb->extracurriculars()->where('extracurricular_id', $request->extracurricular)->exists()) {
                    $studentppdb->extracurriculars()->attach($request->extracurricular);
                }

                return ['message' => 'ok'];
            });
            Route::post('remove', function (Request $request) {
                $user = $request->user();

                $studentppdb = $user->studentppdbs()->where('id', $request?->id)->firstOrFail();

                if ($studentppdb->extracurriculars()->where('extracurricular_id', $request->extracurricular)->exists()) {
                    $studentppdb->extracurriculars()->detach($request->extracurricular);
                }

                return ['message' => 'ok'];
            });
        });
    });

    Route::group(['prefix' => 'attendances'], function () {
        Route::get('/', function (Request $request) {
            return $request->user()->attendances()->paginate(10);
        });
    });
    Route::group(['prefix' => 'subjects'], function () {
        Route::get('/all', function (Request $request) {
            return Subject::all();
        });
    });
    Route::group(['prefix' => 'grades'], function () {
        Route::get('/', function (Request $request) {
            $examresultSubjects = $request->user()->examresults()->get()->groupBy('exam.subject.name');
            $studentassigments = $request->user()->studentassigments()->get()->groupBy('assigment.subject.name');
            $map = [];

            foreach ($examresultSubjects as $subject => $examresult) {
                $types = $examresult->groupBy('exam.examtype.name');

                foreach ($types as $type => $value) {
                    $map[$subject][$type] = $value;
                }
            }
            foreach ($studentassigments as $subject => $value) {
                $map[$subject]["Tugas"] = $value;
            }

            return $map;
        });
    });
    Route::group(['prefix' => 'assigments'], function () {
        Route::post('/{id}', function (Request $request, $id) {
            $user = $request->user();

            $assigment = Assigment::findOrFail($id);

            $studentAssigment = StudentAssigment::firstOrCreate([
                'assigment_id' => $assigment->id,
                'user_id' => $user->id,
            ]);

            if ($studentAssigment->edited_times > 3) {
                return ['message' => 'Anda tidak bisa mengubah tugas lebih dari 3 kali'];
            }

            $studentAssigment->content = $request->content;

            $studentAssigment->external_url = $request->external_url;

            $studentAssigment->attachments()->saveMany(Attachment::whereIn('id', $request->attachments ?? [])->get());

            $studentAssigment->turned_at = now();


            $studentAssigment->increment('edited_times');


            $studentAssigment->save();



            return ['message' => 'ok'];
        });

        Route::get('{id}', function (Request $request, $id) {
            $user = $request->user();

            $assigment = Assigment::with('myanswer', 'classroom', 'subject', 'user')->findOrFail($id);

            if (!$user->myclassrooms->pluck('id')->contains($assigment->classroom->id)) {
                return ['message' => 'unathorized'];
            }

            return $assigment;
        });
    });


    Route::put('/settings', function (Request $request) {
        /**  @var App/Models/User $user  */
        $user = $request->user;
        $user->name = $request->name;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->gender = $request->gender;

        if ($request->hidden_attribute) {
            $user->hidden_attribute = $request->hidden_attribute;
        }

        if ($request->profilepicture) {
            $user->profilepicture?->delete();

            $profilepicture = Attachment::findOrFail($request->profilepicture);
            $profilepicture->role = User::PROFILEPICTURE;
            $user->profilepicture()->save($profilepicture);
        }


        $user->save();
    });
});
Route::group(['middleware' => ['auth:sanctum', EnsureStudent::class], 'prefix' => 'students'], function () {
    Route::group(['prefix' => 'absents'], function () {
        Route::post('/', function (Request $request) {
            $user = $request->user();

            $startAt =  Carbon::parse($request->start_at);
            $finishAt = Carbon::parse($request->finish_at);

            $startAt->hour = now()->hour;
            $startAt->minute = now()->minute;
            $startAt->second = now()->second;

            $finishAt->hour = $startAt->hour;
            $finishAt->minute = $startAt->minute;
            $finishAt->second = $startAt->second;

            $absent = new Absent();
            $absent->receiver_id = $request->teacher;
            $absent->type = $request->type;
            $absent->reason = $request->reason;
            $absent->start_at = $startAt;
            $absent->finish_at = $finishAt;

            $user->absents()->save($absent);
        });

        Route::get('/', fn (Request $request) => $request->user()->absents()->paginate(10));
    });

    Route::group(['prefix' => 'consultations'], function () {
        Route::post('/', function (Request $request) {
            $user = $request->user();

            $constult = new Consultation();
            $constult->consultant_id = User::findOrFail($request->teacher)->id;
            $constult->name = $request->name;
            $constult->school_id = $user->school_id;
            $constult->problem = $request->problem;
            $user->consultations()->save($constult);
        });

        Route::get('/', fn (Request $request) => $request->user()->consultations()->paginate(10));
        Route::get('{id}', fn (Request $request, $id) => $request->user()->consultations()->findOrFail($id));
    });


    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', function (Request $request) {
            return  Article::latest()->where('role', Article::POST)->paginate(10);
        });

        Route::post('/', function (Request $request) {
            $user = $request->user();
            $article = new Article();
            $article->name = $request->name;
            $article->content = $request->content;
            $article->visibility = 'PUBLIK';
            $article->user_id = $user->id;
            $article->role = Article::POST;
            $article->school_id = $request?->school ?? $user?->schools()?->first()?->id;
            $user->articles()->save($article);
            return ['message' => 'ok'];
        });
    });
    Route::group(['prefix' => 'classrooms'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();

            return $user->myclassrooms()->with(
                'students',
                'user',
            )->get();
        });

        Route::post('/join', function (Request $request) {
            $user = $request->user();

            $classroom = Classroom::findOrFail($request->classroom);

            if (!$user->classtype_id) {
                $user->classtype_id = $classroom->classtype_id;
                $user->save();
            }

            if (!$user->classrooms()->where('classrooms.id', $classroom->id)->exists()) {
                $user->myclassrooms()->syncWithoutDetaching($classroom);
            }


            return $user->classrooms;
        });

        Route::get('/all', function (Request $request) {
            $user = $request->user();

            $classrooms  = [];

            foreach ($user->followings()->where('roles', User::TEACHER)->get() as $teacher) {
                if ($user->classtype_id) {
                    $candidateclasrooms = $teacher->classrooms()->where('classtype_id', $user->classtype_id);
                } else {
                    $candidateclasrooms = $teacher->classrooms();
                }

                foreach ($candidateclasrooms->get() as $classroom) {
                    $classrooms[] = $classroom;
                }
            }

            return $classrooms;
        });
    });

    Route::group(['prefix' => 'schools'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();

            return $user->school()->with('homeroomteachers', 'counselors', 'teachers', 'students')->first();
        });
    });

    Route::group(['prefix' => 'events'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();


            $events  = collect([]);

            $now = now();

            $classrooms = $user->myclassrooms();

            foreach ($classrooms->get() as $classroom) {
                $eventsData = Octane::concurrently([
                    function () use ($classroom) {
                        $now = now();

                        $data = $classroom->exams()->with(["examsessions" => function ($q) use ($now) {
                            $q->latest()->where('close_at', '>', $now);
                        }])->get();

                        $data = $data->map(fn ($e) => $e->examsessions)->flatten();

                        $data = $data->map(function ($e) {
                            return [
                                'name' => $e->exam->name,
                                'subject' => $e->exam->subject,
                                'classroom' => $e->exam->classroom,
                                'user' => $e->exam->user,
                                'id' => $e->exam->id,
                                'close_at' => $e->close_at,
                                'open_at' => $e->open_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Exam',
                            ];
                        });
                        return $data;
                    },
                    function () use ($classroom) {
                        $data = $classroom->meetings()->whereNull('finish_at')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'user' => $classroom->user,
                                'id' => $e->id,
                                'close_at' => $e->finish_at,
                                'open_at' => $e->start_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Meeting',
                            ];
                        });
                        return $data;
                    },
                    function () use ($classroom) {
                        $data = $classroom->assigments()->where('close_at', '>', now())->with('user', 'classroom', 'subject')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'user' => $classroom->user,
                                'id' => $e->id,
                                'close_at' => $e->close_at,
                                'open_at' => $e->created_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Assigment',
                            ];
                        });

                        return $data;
                    }

                ]);

                foreach ($eventsData as $event) {
                    foreach ($event as $e) {
                        $events->push($e);
                    }
                }
            }

            return $events;
        });
    });
    Route::group(['prefix' => 'exams'], function () {
        Route::get('{id}/attendances', function (Request $request, $id) {
            $exam = Exam::findOrFail($id);
            $user = $request->user();
            $attendance = Attendance::firstOrCreate([
                'school_id' => $exam->classroom->school_id,
                'user_id' => $user->id,
                'agenda_id' => $exam->agenda->id,
            ]);

            return $attendance;
        });
        Route::post('{id}/reportbegin', function (Request $request, $id) {
            $user = $request->user();


            $exam = Exam::findOrFail($id);


            $attendance = Attendance::firstOrCreate([
                'school_id' => $exam->classroom->school_id,
                'user_id' => $user->id,
                'agenda_id' => $exam->agenda->id,
            ]);

            $attendance->updated_at = now();
            $attendance->attended = true;
            $attendance->save();


            if (!$exam->classroom->students()->where('user_id', $user->id)->exists()) {
                return response(['message' => 'Anda Tidak Memiliki Akses ulangan ini !'], 401);
            }

            $examsession = Examsession::findOrFail($request->examsession);

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'session is over'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'wrong'];
            }

            $examtracker = Examtracker::firstOrCreate([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'examsession_id' => $examsession->id,
            ]);

            if ($examresult = Examresult::where('examsession_id', $request->examsession)
                ->where('user_id', $user->id)
                ->where('exam_id', $exam->id)->exists()
            ) {
                $studentanswers = StudentAnswer::where([
                    'user_id' => $user->id,
                    'examsession_id' => $examsession->id,
                    'exam_id' => $exam->id
                ])->get();
                return ['message' => 'already reported', 'examtracker' => $examtracker, 'studentanswers' => $studentanswers];
            }

            $examresult = Examresult::firstOrCreate([
                'examsession_id' => $examsession->id,
                'user_id' => $user->id,
                'exam_id' => $exam->id
            ]);

            $examresult->save();




            return ['message' => 'saved', 'examtracker' => Examtracker::find($examtracker->id)];
        });

        Route::post('{id}/heartbeat', function (Request $request, $id) {
            $user = $request->user();



            $exam = Exam::findOrFail($id);

            $examsession = Examsession::findOrFail($request->examsession);


            $examtracker = Examtracker::firstOrCreate([
                'exam_id' =>   $exam->id,
                'user_id' =>   $user->id,
                'examsession_id' =>   $examsession->id,
            ]);

            $now = now();

            $examtracker->increment('minute_passed');

            $examtracker->last_activity = $now;

            $examtracker->save();

            return ['message' => 'recorded', 'examtracker' => $examtracker];
        });
        Route::post('{id}/finish', function (Request $request, $id) {
            $user = $request->user();


            $exam = Exam::findOrFail($id);

            $examsession = Examsession::findOrFail($request->examsession);

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'session is over'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'wrong'];
            }

            $studentAnswer = StudentAnswer::where('examsession_id', $request->examsession)
                ->where('user_id', $user->id)
                ->where('exam_id', $exam->id);

            $examresult = Examresult::where('examsession_id', $request->examsession)
                ->where('user_id', $user->id)
                ->where('exam_id', $exam->id)->firstOrFail();

            $studentAnswer->update(['examresult_id' => $examresult->id]);

            $examresult->finish_at = now();
            $examresult->grade = $studentAnswer->sum('grade') / $exam->questions()->count();

            $examresult->save();
            return ['message' => 'graded'];
        });
        Route::get('{id}', function (Request $request, $id) {
            $user = $request->user();

            $exam = Exam::with('examsessions')->findOrFail($id);

            $check = $exam->classroom->students()->where('user_id', $user->id)->exists();

            $examresult = Examresult::where('user_id', $user->id)
                ->where('exam_id', $exam->id)->first();

            if ($examresult?->finish_at) {
                return response(['message' => 'Anda sudah mengerjakan ujian ini'], 401);
            }

            if (!$check) {
                return response(['message' => 'Anda Tidak Memiliki Akses ujian ini !'], 401);
            }

            $exam = $exam
                ->with(
                    'questions',
                    'subject',
                    'supervisors',
                    'user',
                    'examtype',
                    'examsessions'
                )
                ->where('id', $exam->id)->first();

            return ['exam' => $exam];
        });

        Route::post('/checktoken', function (Request $request) {
            // /**  @var App/Models/Student $student  */
            // $student = $request->user()->student;

            $examsession = Examsession::findOrFail($request->examsession);

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'session is over'];
            }

            if ($examsession->token == $request->token) {
                return ['message' => 'ok'];
            } else {
                return ['message' => 'wrong'];
            }
        });


        Route::get('/{id}/result', function (Request $request, $id) {
            $student = $request->user();

            return Examresult::where('user_id', $student->id)
                ->where('exam_id', $id)
                ->with(
                    'exam.user',
                    'exam.subject',
                    'exam.examtype',
                    'exam.supervisors',
                    'studentanswers',
                    'user.profilepicture',
                    'examsession'
                )
                ->firstOrFail();
        });





        Route::post('/submitanswer', function (Request $request) {
            $user = $request->user();

            $examsession = Examsession::findOrFail($request->examsession);

            $exam = $examsession->exam;

            $examtracker = Examtracker::where([
                'exam_id' =>  $request->exam,
                'user_id' =>   $user->id,
                'examsession_id' =>   $examsession->id,
            ])->firstOrFail();

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'Sesi telah berakhir atau belum dibuka'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'Token salah'];
            }

            if ($examtracker->minute_passed > $exam->time_limit) {
                return ['message' => 'Waktu anda sudah habis !'];
            }

            $answer  = StudentAnswer::firstOrCreate([
                'question_id' => $request->question,
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'examsession_id' => $request->examsession,
            ]);

            /**
             * Grading Code
             */

            $question = Question::with('correctanswer')->findOrFail($request->question);

            if ($question->type == "MULTI_CHOICE") {
                $answer->grade = $question->correctanswer->id == $request->answer['id'] ? 100 : 0;
                $answer->is_correct = $question->correctanswer->id == $request->answer['id'];
                $answer->answer_id = $request->answer['id'];
                $answer->is_proccessed = true;
            } else {
                $answercontent = strip_tags($request->answer);
                similar_text(strtolower($answercontent), strtolower(strip_tags($question->correctanswer->content)), $percentage);
                $answer->is_correct = $percentage > 75;
                $answer->grade = $percentage;
                $answer->is_proccessed = false;
                $answer->content = $request->answer;
            }

            $answer->save();

            return ['message' => 'saved and graded'];
        });
    });
});
Route::group(['middleware' => ['auth:sanctum', EnsureTeacher::class], 'prefix' => 'teachers'], function () {
    Route::group(['prefix' => 'bookings'], function () {
        Route::put('{id}', function (Request $request, $id) {
            $booking = Booking::findOrFail($id);

            if ($booking->is_approved || $booking->status == Booking::SELESAI) {
                $booking->status = $request->status;
                $booking->save();

                return ['message' => 'ok'];
            } else {
                return response(['message' => 'Client belum menyelesaikan pesanan'], 400);
            }
        });
    });

    Route::group(['prefix' => 'ppdb', 'middleware' => [EnsureAdminSchool::class]], function () {
        Route::group(['prefix' => 'extracurriculars'], function () {
            Route::post('{id}', function (Request $request, $id) {

                $school = School::findOrFail($request->school);

                $school->extracurriculars()->detach($id);

                return ['message' => 'ok'];
            });
            Route::post('', function (Request $request) {

                $school = School::findOrFail($request->school);

                $school->extracurriculars()->syncWithoutDetaching($request->extracurricular);

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'majors'], function () {
            Route::post('{id}', function (Request $request, $id) {

                $school = School::findOrFail($request->school);

                $school->majors()->detach($id);

                return ['message' => 'ok'];
            });
            Route::post('', function (Request $request) {

                $school = School::findOrFail($request->school);

                $school->majors()->syncWithoutDetaching($request->major);

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'application'], function () {
            Route::put('{id}', function (Request $request, $id) {
                $studentppdb = StudentPpdb::findOrFail($id);

                $studentppdb->status = $request->status;

                $studentppdb->changer_id = $request->user()->id;

                $studentppdb->save();

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'forms'], function () {
            Route::post('', function (Request $request) {
                $school = $request->user()->adminschools()->first();

                $formTemplate = new FormTemplate();
                $formTemplate->data = $request->data;
                $formTemplate->save();

                $school->form_template_id = $formTemplate->id;

                $school->save();

                return ['message' => 'ok'];
            });
            Route::put('', function (Request $request) {
                $school = $request->user()->adminschools()->first();

                $formTemplate = $school->ppdbform;
                $formTemplate->data = $request->data;
                $formTemplate->save();

                return ['message' => 'ok'];
            });
            Route::put('{id}', function (Request $request, $id) {
                $form = Form::findOrFail($id);

                $studentppdb  = $form->studentppdb;

                if (!$studentppdb) {
                    return response(['message' => 'no'], 403);
                }

                $form->data = $request->data;
                $form->is_locked = $request->locked;
                $form->is_verified = $request->verify;
                $form->data = $request->data;
                $form->comment = $request->comment;

                $form->save();

                $studentppdb->changer_id = $request->user()->id;
                $studentppdb->wave_id = $request->wave ?? $studentppdb->wave_id;
                $studentppdb->save();

                return ['message' => 'ok'];
            });
        });
        Route::group(['prefix' => 'waves'], function () {
            Route::put('{id}', function (Request $request, $id) {
                $wave = Wave::findOrFail($id);

                $wave->close_at = Carbon::parse($request->close_at);
                $wave->open_at = Carbon::parse($request->open_at);

                $wave->max_join = $request->max_join;
                $wave->education_year_end = $request->education_year_end;
                $wave->education_year_start = $request->education_year_start;

                $wave->allow_extracurricular = $request->allow_extracurricular;
                $wave->allow_major = $request->allow_major;

                if (!in_array($wave->school_id, $request->user()->adminschools->pluck('id')->toArray())) {
                    return response(["message" => "no"], 403);
                }

                $wave->save();

                return ['message' => 'ok'];
            });
        });
    });
    Route::group(['prefix' => 'grades'], function () {
        Route::get('/', function (Request $request) {
            // no efficent por favor
            $classroom = Classroom::findOrFail($request->classroom);
            $map = [];
            $studentMap = [];
            foreach ($classroom->students as $student) {
                $studentMap[$student->name] = $student->id;
                $examresultSubjects = Examresult::where([
                    'user_id' =>  $student->id,
                ])->whereHas('exam', fn ($q) => $q->where('classroom_id', $classroom->id))
                    ->get()
                    ->groupBy('exam.subject.name');

                $studentassigments =  StudentAssigment::where([
                    'user_id' =>  $student->id,
                ])->whereHas('assigment', fn ($q) => $q->where('classroom_id', $classroom->id))
                    ->get()
                    ->groupBy('assigment.subject.name');


                foreach ($examresultSubjects as $subject => $examresult) {
                    $types = $examresult->groupBy('exam.examtype.name');
                    foreach ($types as $type => $value) {
                        $map[$student->name][$subject][$type] = $value;
                    }
                }
                foreach ($studentassigments as $subject => $value) {
                    $map[$student->name][$subject]["Tugas"] = $value;
                }
            }

            return ['grade' => $map, 'studentMap' => $studentMap];
        });
        Route::get('/{id}/student', function (Request $request) {
            // no efficent por favor
            $classroom = Classroom::findOrFail($request->classroom);
            $map = [];
            $examresultSubjects = Examresult::where([
                'user_id' =>  $request->student,
            ])->whereHas('exam', fn ($q) => $q->where('classroom_id', $classroom->id))
                ->get()
                ->groupBy('exam.subject.name');

            $studentassigments =  StudentAssigment::where([
                'user_id' =>  $request->student,
            ])->whereHas('assigment', fn ($q) => $q->where('classroom_id', $classroom->id))
                ->get()
                ->groupBy('assigment.subject.name');


            foreach ($examresultSubjects as $subject => $examresult) {
                $types = $examresult->groupBy('exam.examtype.name');
                foreach ($types as $type => $value) {
                    $map[$subject][$type] = $value;
                }
            }
            foreach ($studentassigments as $subject => $value) {
                $map[$subject]["Tugas"] = $value;
            }

            return ['grade' => $map];
        });
    });
    Route::group(['prefix' => 'autosaves'], function () {
        Route::delete('/{uuid}', function (Request $request, $uuid) {

            $request->user()->autosaves()->where('identifier', $uuid)->delete();

            return response('OK', 200);
        });
        Route::put('/{uuid}', function (Request $request, $uuid) {

            $autosave =  $request->user()->autosaves()->where('identifier', $uuid)->first();

            $autosave->data = $request->data;

            $autosave->save();

            return response('OK', 200);
        });
        Route::post('/', function (Request $request) {
            $autosave = new Autosave();

            $autosave->data = $request->data;
            $autosave->type = $request->type;


            $request->user()->autosaves()->save($autosave);

            return response(['identifier' => $autosave->identifier], 200);
        });
    });
    Route::group(['prefix' => 'attendances'], function () {
        Route::get('/school/{id}', function (Request $request, $id) {
            if ($id == 'undefined') {
                return ['data' => []];
            }
            $school =  $request->user()->schools()->findOrFail($id);
            $attendances = $school->attendances();

            if ($request->roles) {
                $attendances = $attendances->whereHas(
                    'user',
                    function ($q) use ($request) {
                        return $q->where('roles', $request->roles);
                    }
                );
            }

            return $attendances->latest()->paginate(10);
        });
        // Route::get('/', function (Request $request) {
        //     return $request->user()->studentattendances()->paginate(10);
        // });
    });

    Route::group(['prefix' => 'forms'], function () {
        Route::get('/status', function () {
            $maps = [];

            foreach ([
                Form::REQUEST_TUTOR,
                Form::REQUEST_COUNSELOR,
                Form::REQUEST_HEADMASTER,
                Form::REQUEST_ADMIN_SCHOOL,
                Form::REQUEST_HOMEROOM
            ] as $v) {
                $maps[$v] = Form::where('type', $v)->where('status', Form::PENDING)->exists();
            }

            return $maps;
        });

        Route::post('/', function (Request $request) {
            $form = new Form();

            $form->type = $request->type;

            $form->data = $request->data;

            $form->school_id = $request->school;

            $user = $request->user();

            $user->forms()->save($form);

            if ($request->attachment) {
                $attachment = Attachment::findOrFail($request->attachment);
                $attachment->description = $request->description;
                $attachment->role = $request->role;
                $attachment->attachable_id = $form->id;
                $attachment->attachable_type = Form::class;
                $attachment->save();
            }

            // if (config('app.env') == 'local' || config('app.debug') == true) {
            //     dispatch(new FormApproveTest($form));
            // }

            return ['message' => 'ok'];
        });
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/grades/{classroomId}', function (Request $request, $classroomId) {
            /**  @var App/Models/User $user  */
            $user = $request->user();

            $checkSemesterAndSubject = fn ($q) => $q->where('is_odd_semester', $request->isOdd  == "true" ? true : false)->where('subject_id', $request->subject);

            $studentIds = $user->classrooms()->findOrFail($classroomId)->students->pluck('id');

            $checkInClass = fn ($q) => $q->whereHas('user', fn ($qy) => $qy->whereIn('id', $studentIds));

            return $user->classrooms()->where('id', $classroomId)->with([
                'exams' =>  $checkSemesterAndSubject,
                'assigments' => $checkSemesterAndSubject,
                'exams.examresults' => $checkInClass,
                'assigments.studentassigments' => $checkInClass
            ])->firstOrFail();
        });
    });



    Route::group(['prefix' => 'absents'], function () {
        Route::get('/', fn (Request $request) => $request->user()->studentabsents()->latest()->paginate(10));
    });

    Route::group(['prefix' => 'consultations'], function () {
        Route::get('/', function (Request $request) {
            /**  @var App/Models/User $user  */
            $user = $request->user();

            $studentconsultations = $user->studentconsultations()->latest();

            return $studentconsultations->paginate(10);
        });

        Route::get('{id}', function (Request $request, $id) {
            /**  @var App/Models/User $user  */
            $user = $request->user();

            return $user->studentconsultations()->with(['user.myclassrooms' => function ($e) use ($user) {
                return $e->where('user_id', $user->id);
            }])->findOrFail($id);
        });

        Route::put('{id}', function (Request $request, $id) {
            $constult = Consultation::findOrFail($id);

            $constult->notes = $request->notes;
            $constult->advice = $request->advice;

            $constult->save();

            return ['message' => 'ok'];
        });
    });

    Route::group(['prefix' => 'assigments'], function () {
        Route::post('/', function (Request $request) {
            $user = $request->user();

            $assigment = new Assigment();

            $assigment->name = $request->name;

            $assigment->content = $request->content;

            $assigment->classroom_id = $request->classroom;

            $assigment->is_odd_semester = $request->is_odd_semester;

            $assigment->subject_id = $request->subject;

            $assigment->close_at = Carbon::parse($request->close_at);

            $user->assigments()->save($assigment);

            return ['message' => 'ok'];
        });

        Route::get('{id}', function (Request $request, $id) {
            /**  @var App/Models/User $teacher  */
            $user = $request->user();

            return $user->assigments()->with('studentassigments', 'classroom', 'subject', 'user')->findOrFail($id);
        });

        Route::put('{assigmentId}/answers/{studentAssigmentId}', function (Request $request, $assigmentId, $studentAssigmentId) {
            $assigment = Assigment::findOrFail($assigmentId);

            $studentanswer = StudentAssigment::findOrFail($studentAssigmentId);

            if ($assigment->id != $studentanswer->assigment_id) {
                return ['message' => 'ilegal move'];
            }


            $studentanswer->grade = $request->grade;

            $studentanswer->comment = $request->comment;

            $studentanswer->is_graded = true;

            $studentanswer->save();


            return ['message' => 'ok'];
        });
    });
    Route::group(['prefix' => 'meetings'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            return $user->meetings()->latest()->whereNull('finish_at')->get();
        });

        Route::get('{id}', function (Request $request, $id) {
            $user = $request->user();

            return $user->meetings()->firstOrFail($id);
        });

        Route::delete('{id}', function (Request $request, $id) {
            $user = $request->user();

            return $user->meetings()->firstOrFail($id)->delete();
        });

        Route::put('{id}', function (Request $request, $id) {
            $user = $request->user();

            $meeting =  $user->meetings()->findOrFail($id);

            $meeting->name = $request->name ??  $meeting->name;

            $meeting->content = $request->content ??  $meeting->content;

            $meeting->data = $request->data ?? $meeting->data;

            if ($request->finish_at) {
                $meeting->finish_at = now();
            }

            if ($request->article) {
                $meeting->article_id = $request->article == 0 ? null : $request->article;
            }

            if (array_key_exists('attachment', $request?->data ?? [])) {
                try {
                    $attachment = Attachment::find($request->data['attachment']['id']);
                    $meeting->attachments()->save($attachment);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

            $meeting->subject_id = $request->subject_id ?? $meeting->subject_id;
            $meeting->article_id = $request->article_id ?? $meeting->article_id;

            $meeting->save();

            return ['message' => 'ok'];
        });

        Route::put('{meetingId}/{roomId}', function (Request $request, $meetingId, $roomId) {
            $user = $request->user();

            $meeting =  $user->meetings()->findOrFail($meetingId);

            $room = $meeting->rooms()->findOrFail($roomId);

            $room->name = $request->name;

            $room->users()->sync($request->participants ?? []);

            $meeting = Meeting::find($meeting->id);

            broadcast(new MeetingChangeEvent($meeting));

            return $meeting;
        });

        Route::delete('{meetingId}/{roomId}', function (Request $request, $meetingId, $roomId) {
            $user = $request->user();

            $meeting =  $user->meetings()->findOrFail($meetingId);

            $room = $meeting->rooms()->findOrFail($roomId);

            $room->delete();

            $meeting = Meeting::find($meeting->id);

            broadcast(new MeetingChangeEvent($meeting));

            return $meeting;
        });

        Route::put('{meetingId}/', function (Request $request, $meetingId) {
            $user = $request->user();

            $meeting =  $user->meetings()->findOrFail($meetingId);

            $meeting->name = $request->name;
            $meeting->description = $request->description;

            $meeting->save();

            broadcast(new MeetingChangeEvent($meeting));

            return $meeting;
        });

        Route::post('{meetingId}/rooms', function (Request $request, $meetingId,) {
            $user = $request->user();

            $meeting =  $user->meetings()->findOrFail($meetingId);

            $room = new Room();

            $room->name = $request->name;

            $room->identifier = 'meeting.' . $meeting->id;

            $meeting->rooms()->save($room);

            $room->users()->sync([$user->id]);

            $meeting = Meeting::find($meeting->id);

            broadcast(new MeetingChangeEvent($meeting));

            return $meeting;
        });


        Route::post('/', function (Request $request) {
            $user = $request->user();

            $meeting = new Meeting();

            $meeting->name = $request->name;

            $meeting->finish_at = $request->finish_at;

            $meeting->subject_id = $request->subject;
            $meeting->data = $request->data;
            $meeting->article_id = $request->article ?? null;
            $meeting->user_id = $user->id;

            $meeting->start_at = now();

            $classroom = Classroom::findOrFail($request->classroom);

            $classroom->meetings()->save($meeting);

            if ($request->rooms) {
                foreach ($request->rooms as $roomData) {
                    $room = new Room();
                    $room->name = $roomData['name'];
                    $room->identifier = 'meeting.' . $meeting->id;
                    $meeting->rooms()->save($room);

                    $participants = [];

                    foreach ($roomData['users'] as $user) {
                        $participants[] = $user['id'];
                    }

                    $room->users()->attach(array_merge([$user->id], $participants));
                }
            }
            return $meeting;
        });
    });


    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', function (Request $request) {
            return  Article::latest()->where('role', Article::POST)->paginate(10);
        });

        Route::post('/', function (Request $request) {
            $user = $request->user();
            $article = new Article();
            $article->name = $request->name;
            $article->content = $request->content;
            $article->visibility = 'PUBLIK';
            $article->user_id = $user->id;
            $article->role = Article::POST;
            $user->articles()->save($article);
            return ['message' => 'ok'];
        });
    });


    Route::group(['prefix' => 'events'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();


            $events  = collect([]);



            $classrooms = $user->classrooms();

            foreach ($classrooms->get() as $classroom) {
                $eventsData = Octane::concurrently([
                    function () use ($classroom) {
                        $now = now();

                        $data = $classroom->exams()->with(["examsessions" => function ($q) use ($now) {
                            $q->latest()->where('close_at', '>', $now);
                        }])->get();

                        $data = $data->map(fn ($e) => $e->examsessions)->flatten();

                        $data = $data->map(function ($e) {
                            return [
                                'name' => $e->exam->name,
                                'subject' => $e->exam->subject,
                                'classroom' => $e->exam->classroom,
                                'user' => $e->exam->user,
                                'id' => $e->exams->id,
                                'close_at' => $e->close_at,
                                'open_at' => $e->open_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Exam',
                            ];
                        });
                        return $data;
                    },
                    function () use ($classroom) {
                        $data = $classroom->meetings()->whereNull('finish_at')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'user' => $classroom->user,
                                'id' => $e->id,
                                'close_at' => $e->finish_at,
                                'open_at' => $e->start_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Meeting',
                            ];
                        });
                        return $data;
                    },
                    function () use ($user, $classroom) {
                        $data = $user->assigments()->where('close_at', '>', now())->with('user', 'classroom', 'subject')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'user' => $classroom->user,
                                'id' => $e->id,
                                'close_at' => $e->close_at,
                                'open_at' => $e->created_at,
                                'created_at' => $e->created_at,
                                'updated_at' => $e->updated_at,
                                'type' => 'Assigment',
                            ];
                        });

                        return $data;
                    }

                ]);

                foreach ($eventsData as $event) {
                    foreach ($event as $e) {
                        $events->push($e);
                    }
                }
            }

            return $events;
        });
    });

    Route::group(['prefix' => 'feeds'], function () {
        Route::get('/', function (Request $request) {
            return Article::latest()
                ->where('role', Article::POST)
                ->paginate(10);
        });
    });



    Route::group(['prefix' => 'announcements'], function () {
        Route::get('/', function (Request $request) {
            return Article::latest()
                ->where('school_id', $request->user()?->schools()?->first()?->id)
                ->where('role', Article::ANNOUNCEMENT)
                ->paginate(10);
        });

        Route::post('/', function (Request $request) {
            $user = $request->user();
            $article = new Article();
            $article->name = $request->name;
            $article->content = $request->content;
            $article->school_id = $request->school_id;
            $article->visibility = 'PUBLIK';
            $article->user_id = $user->id;
            $article->role = Article::ANNOUNCEMENT;
            $article->school_id = $request?->school ?? $user?->schools()?->first()?->id;
            $user->articles()->save($article);

            if ($request->thumbnail) {
                $thumbnail = Attachment::findOrFail($request->thumbnail);
                $thumbnail->role = Constant::THUMBNAIL;

                $thumbnail->attachable()->associate($article)->save();

                $thumbnail->save();
            }

            return ['message' => 'ok'];
        });
    });

    Route::group(['prefix' => 'theories'], function () {
        Route::get('/', function (Request $request) {
            return Article::latest()->where('role', Article::THEORY)->paginate(10);
        });
        Route::post('/', function (Request $request) {
            $user = $request->user();
            $article = new Article();
            $article->name = $request->name;
            $article->content = $request->content;
            $article->is_paid = $request->is_paid;
            $article->visibility = $request->visibility;
            $article->user_id = $user->id;
            $article->role = Article::THEORY;
            $article->school_id = $request?->school ?? $user?->schools()?->first()?->id;
            $user->articles()->save($article);

            if ($article->is_paid) {
                $price = new Price();
                $price->price = $request->price;

                $article->price()->save($price);
            }


            if ($request->thumbnail) {
                $thumbnail = Attachment::findOrFail($request->thumbnail);

                $thumbnail->role = Constant::THUMBNAIL;

                $thumbnail->attachable()->associate($article)->save();

                $thumbnail->save();
            }
            return ['message' => 'ok'];
        });
    });



    Route::group(['prefix' => 'schooltypes'], function () {
        Route::get('/', function (Request $request) {
            return [$request->user()->schools()->first()->schooltype];
        });
    });

    Route::group(['prefix' => 'classtypes'], function () {
        Route::get('/', function () {
            return Classtype::all();
        });
        Route::get('/myschool', function (Request $request) {
            $classtypes = [];
            foreach ($request->user()->schools()->with('schooltype.classtypes')->get() as $school) {
                foreach ($school->schooltype->classtypes as $classtype) {
                    if (!in_array($classtype, $classtypes)) {
                        $classtypes[] = $classtype;
                    }
                }
            }
            return $classtypes;
        });
    });



    Route::group(['prefix' => 'exams'], function () {
        Route::group(['prefix' => 'examresults'], function () {
            Route::put('{id}', function (Request $request, $id) {
                $examresult = Examresult::findOrFail($id);
                $examresult->grade = $request->grade;
                $examresult->save();
                return 'ok';
            });
            Route::put('{id}/answers/{answerId}', function (Request $request, $id, $answerId) {
                $examresult = Examresult::with('exam')->findOrFail($id);

                $studentanswer = StudentAnswer::findOrFail($answerId);
                $studentanswer->grade = $request->grade;
                $studentanswer->is_correct = $request->is_correct;
                $studentanswer->save();

                $examresult->grade = $examresult->studentanswers()->sum('grade') / $examresult->exam->questions()->count();
                $examresult->save();
                return ['message' => 'ok', 'grade' => $examresult->grade];
            });
        });
        Route::get('/', function (Request $request) {
            return $request->user()->exams()?->paginate(10);
        });
        Route::post('/create', function (Request $request) {
            $user = $request->user();


            $exam = new Exam();

            $exam->name = $request->name;
            $exam->examtype_id = $request->examtype;
            $exam->hint = $request->hint;
            $exam->classroom_id = $request->classroom;
            $exam->description = $request->description;
            $exam->time_limit = $request->time_limit ?? 120;
            $educationyear = explode('/', $request->educationyear);
            $exam->education_year_start = $educationyear[0];
            $exam->education_year_end =  $educationyear[1];
            $exam->is_odd_semester = $request->is_odd_semester;
            $exam->allow_show_result = $request->allow_show_result ?? false;
            $exam->shuffle = $request->shuffle ?? false;

            $exam->subject_id = $request->subject;
            $user->exams()->save($exam);

            $examsessions = [];
            foreach ($request->examsessions as $examsessionData) {
                $examsession = new Examsession();
                $examsession->name = $examsessionData['name'];
                try {
                    $examsession->open_at = Carbon::createFromFormat("Y-m-d H:i", $examsessionData['open_at']);
                    $examsession->close_at = Carbon::createFromFormat("Y-m-d H:i", $examsessionData['close_at']);
                } catch (\Throwable $th) {
                    $examsession->open_at = Carbon::parse($examsessionData['open_at']);
                    $examsession->close_at = Carbon::parse($examsessionData['close_at']);
                }
                $examsession->token = $examsessionData['token'];
                $examsessions[] = $examsession;
            }


            if ($request->packagequestions) {
                Packagequestion::whereIn('id', $request->packagequestions)->update(['editable' => false]);
            }

            if ($request->supervisors) {
                try {
                    $exam->supervisors()->attach($request->supervisors);
                } catch (\Throwable $th) {
                }
            }


            $exam->examsessions()->saveMany($examsessions);
            $exam->questions()->attach($request->questions);

            $exam->questions()->update(['editable' => false]);


            return ['message' => 'success', 'exam' => $exam];
        });

        Route::get('/type', fn () => Examtype::all());
    });

    Route::group(['prefix' => 'headmasters', 'middleware' => [EnsureHeadmaster::class]], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            return $user->headmasterschools()->get();
        });

        Route::group(['prefix' => 'agendas'], function () {
            Route::post('/', function (Request $request) {
                $user = $request->user();

                $agenda = new Agenda();
                $agenda->name = $request->name;
                $agenda->description = $request->description;
                $agenda->school_id = $request->school;
                $agenda->finish_at = Carbon::parse($request->finish_at);

                $user->agendas()->save($agenda);

                $absents = $user->studentabsents()->whereDate('finish_at', '>', now())->get();

                $absentsMap = [];

                foreach ($absents as $value) {
                    $absentsMap[$value->user_id] = $value;
                }


                foreach ($request->users as $user) {
                    if (array_key_exists($user, $absentsMap)) {
                        Attendance::firstOrCreate([
                            'school_id' => $agenda->school_id,
                            'user_id' => $user,
                            'agenda_id' => $agenda->id,
                            'reason' => $absentsMap[$user]->reason,
                        ]);
                    } else {
                        Attendance::firstOrCreate([
                            'school_id' => $agenda->school_id,
                            'user_id' => $user,
                            'agenda_id' => $agenda->id,
                        ]);
                    }
                }

                return 'ok';
            });
        });

        Route::get('/reports', function (Request $request) {
            $user = $request->user();
            return $user->myreports()->paginate(10);
        });
    });



    Route::group(['prefix' => 'homerooms', 'middleware' => [EnsureHomeroom::class]], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            return $user->homeroomschools()->with('homeroomteachers')->get();
        });

        Route::get('/reports', function (Request $request) {
            $user = $request->user();
            return $user->myreports()->paginate(10);
        });
    });

    Route::group(['prefix' => 'reports', 'middleware' => [EnsureGradeReport::class]], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            return $user->reports()->paginate(10);
        });

        Route::get('/{id}', function (Request $request, $id) {
            $user = $request->user();

            try {
                return $user->reports()->where('id', $id)->firstOrFail();
            } catch (\Throwable $th) {
                return $user->myreports()->where('report_id', $id)->firstOrFail();
            }
        });

        Route::post('/', function (Request $request) {
            $report = new Report();
            $report->name = $request->name;
            $report->data = $request->data;
            $report->type = $request->type;

            if ($request->receiver) {
                $report->users()->sync($request->receiver);
            }


            $request->user()->reports()->save($report);

            return $report;
        });

        Route::put('/{id}', function (Request $request, $id) {
            $report = $request->user()->reports()->findOrFail($id);
            $report->name = $request->name;
            $report->data = $request->data;
            $report->type = $request->type;

            if ($request->receiver) {
                $report->users()->attach($request->receiver);
            }


            $report->save();

            return $report;
        });
    });

    Route::group(['prefix' => 'schools'], function () {
        Route::post('/update/thumbnail', function (Request $request) {
            $user = $request->user();

            $school = School::findOrFail($request->school);

            if (!$school->admins()->where('user_id', $user->id)->exists()) {
                return ['message' => 'Unauthorized'];
            }

            if ($request->has('logo')) {
                $school->logo()->delete();
                $newPicture = Attachment::findOrFail($request->logo);
                $newPicture->role = School::LOGO;
                $newPicture->save();
                $school->logo()->save($newPicture);
            }
            if ($request->has('cover')) {
                $school->cover()->delete();
                $newPicture = Attachment::findOrFail($request->cover);
                $newPicture->role = School::COVER;
                $newPicture->save();
                $school->cover()->save($newPicture);
            }

            return ['message' => 'ok'];
        });

        Route::get('/', function (Request $request) {
            return $request->user()->schools()->with(
                'homeroomteachers',
                'counselors',
                'teachers',
                'students',
                'headmasters',
                'admins'
            )->get();
        });

        Route::get('/teachers', function (Request $request) {
            $user = $request->user();
            return $user->schools()->first()->teachers()->with('user')->get();
        });

        Route::get('/myschool', function (Request $request) {
            $user = $request->user();
            return $user->schools()->with('students', 'teachers', 'homeroomteachers', 'counselors')->first();
        });

        Route::get('/subjects/{id}', function (Request $request, $id) {
            $user = $request->user();
            return $user->schools()->first()->subjects()->where('subjects.id', $id)->firstOrFail();
        });

        Route::get('/{id}', function (Request $request, $id) {
            $user = $request->user();
            return $user->schools()->with(
                'homeroomteachers',
                'counselors',
                'teachers',
                'students',
                'headmasters',
                'admins'
            )->findOrFail($id);
        });

        Route::post('/join', function (Request $request) {
            $teacher = $request->user();

            $school = School::findOrFail($request->school);

            $school->teachers()->syncWithoutDetaching($teacher->id);

            return 'ok';
        });
    });

    Route::group(['prefix' => 'classrooms'], function () {
        Route::post('/admit', function (Request $request) {
            $user = $request->user();

            $student = User::findOrFail($request->student)->student;

            if (!$student) {
                return ['message' => 'Siswa tidak ditemukan'];
            }

            $classroom = $user->classrooms()->findOrFail($request->classroom);

            if (!$student->classrooms()->where('classrooms.id', $classroom->id)->exists()) {
                $student->classrooms()->attach($classroom);
            }

            return  ['message' => 'ok'];
        });


        Route::get('/', function (Request $request) {
            $user = $request->user();

            $classrooms =  $user->classrooms();
            if ($request->withExtra) {
                $classrooms = $classrooms->with(
                    'user',
                    'students',
                );
            }

            return $classrooms->get();
        });

        Route::get('/all', function (Request $request) {
            $user = $request->user();

            $classrooms = $user->school->classrooms();

            if ($request->withExtra) {
                return $classrooms->with(
                    'user',
                    'students',
                )->get();
            } else {
                return $classrooms->get();
            }
        });

        Route::post('/add', function (Request $request) {
            $user = $request->user();
            /**  @var App/Models/School $school  */
            $school = School::findOrFail($request->school);


            if (Classroom::where(['user_id' => $user->id, 'name' => $request->name, 'classtype_id' => $request->classtype_id])->exists()) {
                return ['message' => 'exists'];
            }

            $classroom = new Classroom();
            $classroom->name = $request->name;
            $classroom->user_id = $user->id;
            $classroom->classtype_id = $request->classtype_id;
            $school->classrooms()->save($classroom);
        });

        Route::delete('/{id}', function (Request $request, $id) {
            $user = $request->user();

            $user->classrooms()->where('id', $id)->firstOrFail()->delete();

            return ['message' => 'ok'];
        });

        Route::put('/{id}/detach/{studentId}/to/{toId}', function (Request $request, $id, $studentId, $toId) {
            $user = $request->user();


            $classroom = Classroom::findOrFail($id);

            $newClassroom = Classroom::findOrFail($toId);

            $classroom->students()->detach($studentId);
            $newClassroom->students()->attach($studentId);

            return ['message' => 'ok'];
        });

        Route::delete('/{id}/detach/{studentId}', function (Request $request, $id, $studentId) {
            $user = $request->user();


            $classroom = $user->classrooms()->findOrFail($id);

            $classroom->students()->detach($studentId);

            return ['message' => 'ok'];
        });

        Route::put('/{id}', function (Request $request, $id) {
            $user = $request->user();

            $classroom = $user->classrooms()->where('id', $id)->firstOrFail();

            $classroom->name = $request->name;

            $classroom->save();

            return ['message' => 'ok'];
        });
    });



    Route::group(['prefix' => 'questions'], function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();

            $questions = (new Question())->with(
                'classtype.schooltype',
            );

            if ($request->classtype) {
                $questions =  $questions->where('classtype_id', $request->classtype);
            }

            if ($request->subject) {
                $questions =  $questions->where('subject_id', $request->subject);
            }

            if ($request->type) {
                $questions =  $questions->where('type', $request->type);
            }

            if ($request->visibility) {
                if ($request->visibility == "SELECTPEOPLE") {
                } elseif ($request->visibility == "") {
                } else {
                    $questions =  $questions->where('visibility', $request->visibility);
                }
            }


            return $questions->paginate(10);
        });

        Route::get('/package', function (Request $request) {
            $user = $request->user();


            $packagequestions = (new Packagequestion())->with(
                'questions.classtype.schooltype',
                'classtype'
            );


            if ($request->subject) {
                $packagequestions =  $packagequestions->where('subject_id', $request->subject);
            }

            if ($request->classtype) {
                $packagequestions =  $packagequestions->where('classtype_id', $request->classtype);
            }

            if ($request->type) {
                $packagequestions =  $packagequestions->where('type', $request->type);
            }
            if ($request->visibility) {
                if ($request->visibility == "SELECTPEOPLE") {
                } elseif ($request->visibility == "") {
                } else {
                    $packagequestions =  $packagequestions->where('visibility', $request->visibility);
                }
            }


            return $packagequestions->paginate(10);
        });



        Route::post('/create', function (Request $request) {
            $user = $request->user();

            $questionIds = [];
            foreach ($request->questions as $questionData) {
                $question = new Question();

                $question->content = $questionData['content'];

                $question->visibility = $request['visibility'];

                $question->classtype_id = $request['classtype'];

                $question->type = $questionData['type'];


                $question->subject_id = $request['subject'];

                $user->questions()->save($question);

                $questionIds[] = $question->id;

                $answers = [];
                foreach ($questionData['answers'] as $i => $answerData) {
                    $answer = new Answer();

                    if ($questionData['type'] !== 'MULTI_CHOICE') {
                        $answer->is_correct = true;
                    } else {
                        $answer->is_correct = $i == $questionData['correctanswer'];
                    }

                    $answer->content = $answerData['content'] ?? '';
                    $answers[] = $answer;
                }

                $question->answers()->saveMany($answers);

                // $answers  = $question->answers;

                if (array_key_exists('answerattachments', $questionData)) {
                    foreach ($questionData['answerattachments'] as $i => $attachment) {
                        if (!empty($attachment)) {
                            Attachment::find($attachment)->attachable()->associate($answers[$i])->save();
                        }
                    }
                }


                foreach (Attachment::whereIn('id', $questionData['attachments'])->get() as $attachment) {
                    $attachment->attachable()->associate($question)->save();
                }
            }

            if ($request->packagequestion) {
                $packagequestion = new Packagequestion();
                $packagequestion->name = $request->packagequestion;
                $packagequestion->subject_id = $request['subject'];
                $packagequestion->classtype_id = $request['classtype'];
                $packagequestion->user_id = $user->id;
                $packagequestion->visibility = $request['visibility'];
                $packagequestion->save();

                $packagequestion->questions()->attach($questionIds);
            }


            return ['message' => 'success'];
        });
    });


    Route::group(['prefix' => 'subjects'], function () {
        Route::get('/all', function (Request $request) {
            return Subject::all();
        });

        Route::get('/notin', function (Request $request) {
            $user = $request->user();
            return Subject::whereNotIn('id', $user->subjects->map(fn ($e) => $e->id))->get();
        });

        Route::get('/', function (Request $request) {
            $user = $request->user();
            return $user->subjects;
        });

        Route::get('/addable', function (Request $request) {
            $subjectIds = $request->user()->subjects->map(fn ($e) => $e->id);
            return Subject::whereNotIn('id', $subjectIds)->get();
        });

        Route::get('{id}', fn ($id) => Subject::findOrFail($id));

        Route::post('/remove', function (Request $request) {
            $subject = Subject::findOrFail($request->id);
            $user = $request->user();
            if (!$user->subjects()->where('id', $request->id)->exists()) {
                $user->subjects()->detach($subject);
                return ['message' => 'success'];
            }
            return ['message' => 'success'];
        });

        Route::post('/add', function (Request $request) {
            $subjects = Subject::whereIn('id', $request->ids)->get();
            $user = $request->user();
            foreach ($subjects as  $subject) {
                if (!$user->subjects()->where('subjects.id', $subject->id)->exists()) {
                    $user->subjects()->save($subject);
                }
            }
            return ['message' => 'success', 'ids' => $request->ids];
        });

        Route::put('/{id}', function (Request $request, $id) {
            $user = $request->user();

            $subject = $user->subjects()->where('subjects.id', $id)->firstOrFail();

            $subject->pivot->kkm = $request->kkm;

            $subject->pivot->save();

            return ['message' => 'success'];
        });


        Route::delete('/{id}', function (Request $request, $id) {
            $user = $request->user();

            $user->subjects()->detach([$id]);

            return ['message' => 'success'];
        });
    });
});
