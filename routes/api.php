<?php

use App\Actions\Attachment\Upload;
use App\Events\MeetingChangeEvent;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\EnsureStudent;
use App\Http\Middleware\EnsureTeacher;
use App\Models\Absent;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Assigment;
use App\Models\Attachment;
use App\Models\Attendance;
use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Examresult;
use App\Models\Examsession;
use App\Models\Examtracker;
use App\Models\Examtype;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Packagequestion;
use App\Models\Price;
use App\Models\PrivateRoom;
use App\Models\Province;
use App\Models\Question;
use App\Models\Room;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\StudentAnswer;
use App\Models\StudentAssigment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Broadcast;
use Laravel\Octane\Facades\Octane;

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

Route::get('examtypes', fn () => Examtype::all());
Route::get('/provinces', fn () => Province::all());
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

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'meetings'], function () {
    Route::get('/{id}', function (Request $request, $id) {

        $user = $request->user();

        $studentconsultations = $user->studentconsultations()->latest();

        if ($user->roles == "TEACHER") {
            return $user->meetings()->with('classroom.students')->findOrFail($id);
        } else {
            $meeting = Meeting::with('classroom.students')->with(['rooms' => function ($e) use ($user) {
                return $e->whereHas('users', fn ($e) => $e->where('user_id', $user->id));
            }])->findOrFail($id);

            $attendance = Attendance::firstOrCreate([
                'subject_id' => $meeting->subject_id,
                'classroom_id' => $meeting->classroom_id,
                'user_id' => $user->id,
                'attendable_id' => $meeting->id,
                'attendable_type' => Meeting::class
            ]);

            $attendance->updated_at = now();

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



    Route::get('mark-read-all', function (Request $request) {

        $request->user()->unreadNotifications->markAsRead();

        return ['message' => 'ok'];
    });

    Route::get('notifications', function (Request $request) {

        return $request->user()->notifications;
    });


    Route::put('/', function (Request $request) {
        $user = $request->user();

        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->dob = Carbon::parse($request->dob);
        $user->gender = $request->gender;
        $user->address = $request->address;

        $user->specialty = $request->specialty;
        $user->academic_degree = $request->academic_degree;

        if ($request->hidden_attribute) {
            $user->hidden_attribute = json_encode($request->hidden_attribute);
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

    Route::group(['prefix' => 'attachments'], function () {

        Route::post('/temp', function (Request $request) {

            $files = $request->file('file');
            $isProcessed = (bool) $request->get('is_proccessed') ?? false;
            $originalSize = (int) $request->get('original_size') ?? false;
            $compressedSize = (int) $request->get('compressed_size') ?? false;
            $attachment =  Upload::handle($files, $isProcessed, $originalSize, $compressedSize);

            return $attachment;
        });
    });


    Route::group(['prefix' => 'messages'], function () {

        Route::group(['prefix' => 'private'], function () {

            Route::get('/targets', function (Request $request) {
                $user = $request->user();

                $y = $user->school;

                return array_merge($y->teachers->toArray(), $y->students->toArray());
            });
            Route::get('rooms', function (Request $request) {

                $sender = $request->user();

                return PrivateRoom::where('first_id', $sender->id)->orWhere('second_id', $sender->id)->latest('updated_at')->get();
            });
            Route::get('rooms/{id}', function (Request $request, $id) {

                if ($id == 'undefined') return ['message' => 'ok'];

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

                return PrivateRoom::firstOrCreate([
                    'first_id' => $first,
                    'second_id' => $second
                ]);
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

            $assigment = Assigment::with('myanswer', 'classroom', 'subject', 'teacher')->findOrFail($id);

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
            $user->hidden_attribute = json_encode($request->hidden_attribute);
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
            $absent->teacher_id = $request->teacher;
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
            $constult->teacher_id = User::findOrFail($request->teacher)->id;
            $constult->title = $request->title;
            $constult->problem = $request->problem;
            $user->consultations()->save($constult);
        });

        Route::get('/', fn (Request $request) => $request->user()->consultations()->paginate(10));
        Route::get('{id}', fn (Request $request, $id) => $request->user()->consultations()->findOrFail($id));
    });

    Route::group(['prefix' => 'followers'], function () {

        Route::get('/following', function (Request $request) {
            $user = $request->user();

            return $user->followings;
        });

        Route::get('/myfollowing', function (Request $request) {

            return $request->user()->followings;
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

            $student->followings()->detah($request->user);

            return ['message' => 'ok'];
        });


        Route::post('/request', function (Request $request) {
            $user = $request->user();

            if ($user->id == $request->user) {
                return ['message' => 'Anda tidak bisa mengikuti diri sendiri'];
            }

            $candidate = User::findOrFail($request->user);

            if (
                $candidate->roles == "TEACHER" &&
                !$user->school->teachers->pluck('id')->contains($request->user)
            ) {
                return ['message' => 'Guru tidak ada di sekolah'];
            }

            if ($candidate->roles == "TEACHER") {
                $candidate->requestfollowers()->syncWithoutDetaching([$user->id]);
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
            $article->school_id = $request?->school ?? $user?->school?->id;
            $user->articles()->save($article);
            return ['message' => 'ok'];
        });
    });
    Route::group(['prefix' => 'classrooms'], function () {

        Route::get('/', function (Request $request) {
            $user = $request->user();

            return $user->myclassrooms()->with(
                'students',
                'teacher',
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

            return $user->school()->with(
                'schooltype',
                'teachers.province',
            )->first();
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
                                'teacher' => $e->exam->teacher,
                                'id' => $e->id,
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
                                'teacher' => $classroom->teacher,
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
                        $data = $classroom->assigments()->where('close_at', '>', now())->with('teacher', 'classroom', 'subject')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'teacher' => $classroom->teacher,
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

        Route::post('{id}/reportbegin', function (Request $request, $id) {
            $user = $request->user();


            $exam = Exam::findOrFail($id);


            $attendance = Attendance::firstOrCreate([
                'subject_id' => $exam->subject_id,
                'classroom_id' => $exam->classroom_id,
                'user_id' => $user->id,
                'attendable_id' => $exam->id,
                'attendable_type' => Exam::class
            ]);

            $attendance->updated_at = now();

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

            $exam = Exam::findOrFail($id);

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
                    'teacher',
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
                    'exam.teacher',
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


    Route::group(['prefix' => 'reports'], function () {
        Route::get('/grades/{classroomId}', function (Request $request, $classroomId) {
            /**  @var App/Models/User $user  */
            $user = $request->user();

            $checkSemesterAndSubject = fn ($q) => $q->where('is_odd_semester', $request->isOdd  == "true" ?  true : false)->where('subject_id', $request->subject);

            $studentIds = $user->classrooms()->findOrFail($classroomId)->students->pluck('id');

            $checkInClass = fn ($q) => $q->whereHas('user', fn ($qy) => $qy->whereIn('id', $studentIds));

            return $user->classrooms()->where('id', $classroomId)->with([
                'exams' =>  $checkSemesterAndSubject, 'assigments' => $checkSemesterAndSubject,
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
                return $e->where('teacher_id', $user->id);
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
    Route::group(['prefix' => 'followers'], function () {

        Route::get('/myfollowing', function (Request $request) {
            $user = $request->user();
            return $user->followings;
        });

        Route::post('/unfollow', function (Request $request) {
            /**  @var App/Models/User $teacher  */
            $user = $request->user();

            $user->followings()->detach($request->user);

            return ['message' => 'ok'];
        });

        Route::get('/myfollower', function (Request $request) {
            /**  @var App/Models/User $teacher  */
            $user = $request->user();
            return $user->followers;
        });

        Route::post('/accept', function (Request $request) {
            $user = $request->user();

            $user->requestfollowers()->whereIn('follower_id', $request->acceptIds)->update(['is_accepted' => true]);

            return ['message' => 'ok'];
        });

        Route::post('/deny', function (Request $request) {
            $user = $request->user();

            $user->requestfollowers()->detach($request->userId);

            return ['message' => 'ok'];
        });

        Route::post('/request', function (Request $request) {
            $user = $request->user();

            if ($user->id == $request->user) {
                return ['message' => 'you cannot follow yourself'];
            }
            $candidate = User::findOrFail($request->user);


            if ($candidate->roles !== "TEACHER" || !$candidate->teacher) {
                return ['message' => 'invalid follow target'];
            }

            $candidate->teacher->requestfollowers()->syncWithoutDetaching([$user?->id]);

            return ['message' => 'ok'];
        });

        Route::get('/myrequests', function (Request $request) {
            /**  @var App/Models/User $teacher  */
            $user = $request->user();
            return $user->requestfollowers()->with('province')->get();
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

            return $user->assigments()->with('studentassigments', 'classroom', 'subject', 'teacher')->findOrFail($id);
        });

        Route::put('{assigmentId}/answers/{studentAssigmentId}', function (Request $request, $assigmentId, $studentAssigmentId) {


            $assigment = Assigment::findOrFail($assigmentId);

            $studentanswer = StudentAssigment::findOrFail($studentAssigmentId);

            if ($assigment->id != $studentanswer->assigment_id) return ['message' => 'ilegal move'];


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
            $meeting->teacher_id = $user->id;

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
            $article->school_id = $request?->school ?? $user?->school?->id;
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
                                'teacher' => $e->exam->teacher,
                                'id' => $e->id,
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
                                'teacher' => $classroom->teacher,
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
                        $data = $user->assigments()->where('close_at', '>', now())->with('teacher', 'classroom', 'subject')->get();

                        $data = $data->map(function ($e) use ($classroom) {
                            return [
                                'name' => $e->name,
                                'subject' => $classroom->subject,
                                'classroom' => $classroom,
                                'teacher' => $classroom->teacher,
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
                ->where('school_id', $request->user()?->school?->id)
                ->where('role', Article::ANNOUNCEMENT)
                ->paginate(10);
        });

        Route::post('/', function (Request $request) {
            $user = $request->user();
            $article = new Article();
            $article->name = $request->name;
            $article->content = $request->content;
            $article->visibility = 'PUBLIK';
            $article->user_id = $user->id;
            $article->role = Article::ANNOUNCEMENT;
            $article->school_id = $request?->school ?? $user?->school?->id;
            $user->articles()->save($article);

            if ($request->thumbnail) {
                $thumbnail = Attachment::findOrFail($request->thumbnail);
                $thumbnail->role = Article::THUMBNAIL;

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
            $article->school_id = $request?->school ?? $user?->school?->id;
            $user->articles()->save($article);

            if ($article->is_paid) {
                $price = new Price();
                $price->price = $request->price;

                $article->price()->save($price);
            }


            if ($request->thumbnail) {
                $thumbnail = Attachment::findOrFail($request->thumbnail);

                $thumbnail->role = Article::THUMBNAIL;

                $thumbnail->attachable()->associate($article)->save();

                $thumbnail->save();
            }
            return ['message' => 'ok'];
        });
    });



    Route::group(['prefix' => 'schooltypes'], function () {
        Route::get('/', function (Request $request) {
            return [$request->user()->school?->schooltype];
        });
    });

    Route::group(['prefix' => 'classtypes'], function () {

        Route::get('/', function () {

            return Classtype::all();
        });
        Route::get('/myschool', function (Request $request) {
            return $request->user()?->school?->schooltype?->classtypes;
        });
    });



    Route::group(['prefix' => 'exams'], function () {

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
                $examsession->open_at = Carbon::createFromFormat("Y-m-d H:i",  $examsessionData['open_at']);
                $examsession->close_at = Carbon::createFromFormat("Y-m-d H:i",  $examsessionData['close_at']);
                $examsession->token = $examsessionData['token'];
                $examsessions[] = $examsession;
            }


            if ($request->packagequestions) {
                Packagequestion::whereIn('id', $request->packagequestions)->update(['editable' => false]);
            }


            $exam->examsessions()->saveMany($examsessions);
            $exam->questions()->attach($request->questions);

            $exam->questions()->update(['editable' => false]);


            return ['message' => 'success', 'exam' => $exam];
        });

        Route::get('/type', fn () => Examtype::all());
    });


    Route::group(['prefix' => 'schools'], function () {

        Route::get('/', function (Request $request) {

            return $request->user()->schools;
        });

        Route::get('/teachers', function (Request $request) {
            $user = $request->user();
            return $user->school->teachers()->with('user')->get();
        });

        Route::get('/myschool', function (Request $request) {
            $user = $request->user();
            return $user->school()->with('students', 'teachers')->first();
        });

        Route::get('/subjects/{id}', function (Request $request, $id) {
            $user = $request->user();
            return $user->school()->subjects()->where('subjects.id', $id)->firstOrFail();
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
                    'teacher',
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
                    'teacher',
                    'students',
                )->get();
            } else {
                return $classrooms->get();
            }
        });

        Route::post('/add', function (Request $request) {
            $user = $request->user();
            /**  @var App/Models/School $school  */
            $school = $user->school;


            if (Classroom::where(['teacher_id' => $user->id, 'name' => $request->name, 'classtype_id' => $request->classtype_id])->exists()) {
                return ['message' => 'exists'];
            }

            $classroom = new Classroom();
            $classroom->name = $request->name;
            $classroom->teacher_id = $user->id;
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
                } else if ($request->visibility == "") {
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
                } else if ($request->visibility == "") {
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

                foreach ($questionData['answerattachments'] as $i => $attachment) {
                    if (!empty($attachment)) {
                        Attachment::find($attachment)->attachable()->associate($answers[$i])->save();
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
