<?php

use App\Actions\Attachment\Upload;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\EnsureStudent;
use App\Http\Middleware\EnsureTeacher;
use App\Models\Answer;
use App\Models\Attachment;
use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\District;
use App\Models\Exam;
use App\Models\Examresult;
use App\Models\Examsession;
use App\Models\Examtracker;
use App\Models\Examtype;
use App\Models\Packagequestion;
use App\Models\Province;
use App\Models\Question;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\StudentAnswer;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

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


Route::get('me', fn (Request $request) => $request->user());

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

Route::get('/test', fn () => Schooltype::withCount('schools')->get());



Route::post("/token", [ApiAuthController::class, "token"]);
Route::post("/login", [ApiAuthController::class, "login"]);
Route::post("/register", [ApiAuthController::class, "register"]);
Route::middleware('auth:sanctum')->get("/user", [ApiAuthController::class, 'profile']);
Route::middleware('auth:sanctum')->get("/refresh", [ApiAuthController::class, 'refresh']);

Route::get('/attachments/{id}', fn ($id) => Attachment::findOrFail($id));

Route::get('/questions', function (Request $request) {
    $questions = (new Question())->with('classtypes', 'classtypes.schooltype', 'subjects');

    if ($request->classtypes) {
        return  $questions =  $questions->whereHas('classtypes', fn ($q) => $q->whereIn('classtype_id', $request->classtypes))->get();
    }
    if ($request->subjects) {
        $questions =  $questions->whereHas('subjects', fn ($q) => $q->whereIn('subject_id', $request->subjects));
    }

    if ($request->schooltypes) {
        $questions =  $questions->whereHas('classtypes.schooltype', fn ($q) => $q->whereIn('schooltype_id', $request->schooltypes));
    }

    return $questions->paginate(10);
});


Route::get('/{id}/rank', function (Request $request, $id) {
    /**  @var App/Models/Student $student  */

    return Examresult::where('exam_id', $id)
        ->orderBy('grade', 'DESC')
        ->with(
            'student',
        )
        ->get();
});


Route::group(['middleware' => ['auth:sanctum', EnsureStudent::class], 'prefix' => 'students'], function () {

    Route::group(['prefix' => 'classrooms'], function () {
        Route::get('/', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            return $student->classrooms()->with(
                'students',
                'teacher',
                'subject',
            )->get();
        });
        Route::post('/join', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            $classroom = Classroom::findOrFail($request->classroom);

            if (!$student->classrooms()->where('classrooms.id', $classroom->id)->exists()) {
                $student->classrooms()->attach($classroom);
            }


            return $student->classrooms;
        });

        Route::get('/all', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            // TODO : CHECK FOLLOW

            return $student->school->classrooms;
        });
    });
    Route::group(['prefix' => 'schools'], function () {
        Route::get('/', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            return $student->school()->with(
                'schooltype',
                'students.province',
                'teachers.province',
                'teachers.school'
            )->first();
        });
    });

    Route::group(['prefix' => 'events'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;


            $events  = collect([]);

            $now = now();

            $classrooms = $student->classrooms();

            foreach ($classrooms->get() as $classroom) {



                $data = $classroom->exams()->with(["examsessions" => function ($q) use ($now) {
                    $q->where('close_at', '>', $now);
                }])->get();

                // return ;

                $events->push($data->map(fn ($e) => $e->examsessions)->flatten());
            }

            return $events->flatten();
        });
    });
    Route::group(['prefix' => 'exams'], function () {

        Route::post('{id}/reportbegin', function (Request $request, $id) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;


            $exam = Exam::findOrFail($id);

            $check = false;

            foreach ($exam->classrooms as $classroom) {
                if ($classroom->students()->where('students.id', $student->id)->exists()) {
                    $check = true;
                    break;
                }
            }

            if (!$check) {
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
                'student_id' => $student->id,
                'examsession_id' => $examsession->id,
            ]);

            if ($examresult = Examresult::where('examsession_id', $request->examsession)
                ->where('student_id', $student->id)
                ->where('exam_id', $exam->id)->exists()
            ) {
                return ['message' => 'already reported', 'examtracker' => $examtracker];
            }

            $examresult = Examresult::firstOrCreate([
                'examsession_id' => $examsession->id,
                'student_id' => $student->id,
                'exam_id' => $exam->id
            ]);

            $examresult->save();



            return ['message' => 'saved', 'examtracker' => Examtracker::find($examtracker->id)];
        });

        Route::post('{id}/heartbeat', function (Request $request, $id) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;



            $exam = Exam::findOrFail($id);

            $examsession = Examsession::findOrFail($request->examsession);


            $examtracker = Examtracker::firstOrCreate([
                'exam_id' =>   $exam->id,
                'student_id' =>   $student->id,
                'examsession_id' =>   $examsession->id,
            ]);

            $now = now();

            $examtracker->increment('minute_passed');

            $examtracker->last_activity = $now;

            $examtracker->save();

            return ['message' => 'recorded', 'examtracker' => $examtracker];
        });
        Route::post('{id}/finish', function (Request $request, $id) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;


            $exam = Exam::findOrFail($id);

            $examsession = Examsession::findOrFail($request->examsession);

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'session is over'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'wrong'];
            }

            $studentAnswer = StudentAnswer::where('examsession_id', $request->examsession)
                ->where('student_id', $student->id)
                ->where('exam_id', $exam->id);

            $examresult = Examresult::where('examsession_id', $request->examsession)
                ->where('student_id', $student->id)
                ->where('exam_id', $exam->id)->firstOrFail();

            $studentAnswer->update(['examresult_id' => $examresult->id]);

            $examresult->finish_at = now();
            $examresult->grade = $studentAnswer->sum('grade') / $exam->questions()->count();

            $examresult->save();
            return ['message' => 'graded'];
        });
        Route::get('{id}', function (Request $request, $id) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;


            $exam = Exam::findOrFail($id);

            $check = false;

            foreach ($exam->classrooms as $classroom) {
                if ($classroom->students()->where('students.id', $student->id)->exists()) {
                    $check = true;
                    break;
                }
            }

            if (!$check) {
                return response(['message' => 'Anda Tidak Memiliki Akses ulangan ini !'], 401);
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
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            return Examresult::where('student_id', $student->id)
                ->where('exam_id', $id)
                ->with(
                    'exam.teacher',
                    'exam.subject',
                    'exam.examtype',
                    'exam.supervisors',
                    'studentanswers',
                    'student',
                    'examsession'
                )
                ->firstOrFail();
        });





        Route::post('/submitanswer', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            $examsession = Examsession::findOrFail($request->examsession);

            $exam = $examsession->exam;

            $examtracker = Examtracker::where([
                'exam_id' =>  $request->exam,
                'student_id' =>   $student->id,
                'examsession_id' =>   $examsession->id,
            ])->firstOrFail();

            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'Sesi elah berakhir'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'Token salah'];
            }

            if ($examtracker->minute_passed > $exam->time_limit) {
                return ['message' => 'Waktu anda sudah habis !'];
            }

            $answer  = StudentAnswer::firstOrCreate([
                'question_id' => $request->question,
                'student_id' => $student->id,
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
                similar_text($answercontent, strip_tags($question->correctanswer->content), $percentage);
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

    Route::group(['prefix' => 'events'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;


            $events  = collect([]);

            $now = now();

            $classrooms = $teacher->classrooms();

            foreach ($classrooms->get() as $classroom) {
                $data = $classroom->exams()->with(["examsessions" => function ($q) use ($now) {
                    $q->latest()->where('close_at', '>', $now);
                }])->get();

                // return ;

                $events->push($data->map(fn ($e) => $e->examsessions)->flatten());
            }

            return $events->flatten();
        });
    });


    Route::group(['prefix' => 'attachments'], function () {

        Route::post('/temp', function (Request $request) {

            $files = $request->file('file');

            $attachment =  Upload::handle($files);

            return $attachment;
        });
    });

    Route::group(['prefix' => 'schooltypes'], function () {
        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return [$teacher->school->schooltype];
        });
    });

    Route::group(['prefix' => 'classtypes'], function () {

        Route::get('/', function () {

            return Classtype::all();
        });
        Route::get('/myschool', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->school->classtypes;
        });
    });



    Route::group(['prefix' => 'exams'], function () {

        Route::post('/create', function (Request $request) {

            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;


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
            $teacher->exams()->save($exam);

            $examsessions = [];
            foreach ($request->examsessions as $examsessionData) {
                $examsession = new Examsession();
                $examsession->name = $examsessionData['name'];
                $examsession->open_at = Carbon::createFromFormat("Y-m-d H:i",  $examsessionData['open_at']);
                $examsession->close_at = Carbon::createFromFormat("Y-m-d H:i",  $examsessionData['close_at']);
                $examsession->token = $examsessionData['token'];
                $examsessions[] = $examsession;
            }

            $exam->examsessions()->saveMany($examsessions);
            $exam->questions()->attach($request->questions);

            return ['message' => 'success', 'exam' => $exam];
        });

        Route::get('/type', fn () => Examtype::all());
    });


    Route::group(['prefix' => 'schools'], function () {

        Route::get('/fulltime', function (Request $request) {
        });

        Route::get('/teachers', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->school->teachers()->with('user')->get();
        });

        Route::get('/myschool', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->school()->with(
                'teachers',
                'students',
            )->get();
        });

        Route::get('/subjects/{id}', function (Request $request, $id) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->school()->subjects()->where('subjects.id', $id)->firstOrFail();
        });
    });

    Route::group(['prefix' => 'classrooms'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            $classrooms =  $teacher->classrooms();
            if ($request->withExtra) {
                $classrooms = $classrooms->with(
                    'teacher',
                    'subject',
                    'students',
                );
            }

            if ($request->subjects) {
                $classrooms = $classrooms->where('subject_id', $request->subject);
            }

            return $classrooms->get();
        });

        Route::get('/all', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            if ($request->withExtra) {
                return $teacher->school->classrooms()->with(
                    'teacher',
                    'subject',
                    'students',
                )->get();
            } else {
                return $teacher->school->classrooms;
            }
        });

        Route::post('/add', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            /**  @var App/Models/School $school  */
            $school = $teacher->school;

            $classroom = new Classroom();
            $classroom->name = "Kelas " . $request->classtype_level . " " .  $request->name;
            $classroom->teacher_id = $teacher->id;
            $classroom->classtype_id = $request->classtype_id;
            $classroom->subject_id = $request->subject_id;
            $school->classrooms()->save($classroom);
        });
    });



    Route::group(['prefix' => 'questions'], function () {

        Route::get('/', function (Request $request) {

            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            $questions = (new Question())->with(
                'classtypes',
                'classtypes.schooltype',
            );

            if ($request->classtypes) {
                $questions =  $questions->whereHas('classtypes', fn ($q) => $q->whereIn('classtype_id', $request->classtypes));
            } else {
                $questions =  $questions->whereHas('classtypes', fn ($q) => $q->whereIn('classtype_id', $teacher->school->classtypes->map(fn ($e) => $e->id)));
            }
            if ($request->subject) {
                $questions =  $questions->where('subject_id', $request->subject);
            }

            if ($request->schooltypes) {
                $questions =  $questions->whereHas('classtypes.schooltype', fn ($q) => $q->whereIn('schooltype_id', $request->schooltypes));
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

            $packagequestions = (new Packagequestion())->with(
                'questions.classtypes',
                'questions.classtypes.schooltype',
            );

            if ($request->classtypes) {
                $packagequestions =  $packagequestions->whereHas('questions.classtypes', fn ($q) => $q->whereIn('classtype_id', $request->classtypes));
            }
            if ($request->subject) {
                $packagequestions =  $packagequestions->where('subject_id', $request->subject);
            }

            if ($request->schooltypes) {
                $packagequestions =  $packagequestions->whereHas('questions.classtypes.schooltype', fn ($q) => $q->whereIn('schooltype_id', $request->schooltypes));
            }

            if ($request->type) {
                $packagequestions =  $packagequestions->where('questions.type', $request->type);
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
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;






            $questionIds = [];
            foreach ($request->questions as $questionData) {
                $question = new Question();

                $question->content = $questionData['content'];

                $question->visibility = $request['visibility'];

                $question->type = $questionData['type'];


                $question->subject_id = $request['subject'];

                $teacher->questions()->save($question);

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

                $question->classtypes()->attach($request['classtypes']);

                foreach (Attachment::whereIn('id', $questionData['attachments'])->get() as $attachment) {
                    $attachment->attachable()->associate($question)->save();
                }
            }

            if ($request->packagequestion) {
                $packagequestion = new Packagequestion();
                $packagequestion->name = $request->packagequestion;
                $packagequestion->subject_id = $request['subject'];
                $packagequestion->teacher_id = $teacher->id;
                $packagequestion->visibility = $request['visibility'];
                $packagequestion->save();

                $packagequestion->questions()->attach($questionIds);
            }


            return ['message' => 'success'];
        });
    });


    Route::group(['prefix' => 'subjects'], function () {

        Route::get('/all', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            return Subject::all();
        });

        Route::get('/notin', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return Subject::whereNotIn('id', $teacher->subjects->map(fn ($e) => $e->id))->get();
        });

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->subjects;
        });

        Route::get('/addable', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $subjectIds = $request->user()->teacher->subjects->map(fn ($e) => $e->id);
            return Subject::whereNotIn('id', $subjectIds)->get();
        });

        Route::get('{id}', fn ($id) => Subject::findOrFail($id));

        Route::post('/remove', function (Request $request) {
            $subject = Subject::findOrFail($request->id);
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            if (!$teacher->subjects()->where('id', $request->id)->exists()) {
                $teacher->subjects()->detach($subject);
                return ['message' => 'success'];
            }
            return ['message' => 'success'];
        });

        Route::post('/add', function (Request $request) {
            $subjects = Subject::whereIn('id', $request->ids)->get();
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            foreach ($subjects as  $subject) {
                if (!$teacher->subjects()->where('subjects.id', $subject->id)->exists()) {
                    $teacher->subjects()->save($subject);
                }
            }
            return ['message' => 'success', 'ids' => $request->ids];
        });

        Route::put('/{id}', function (Request $request, $id) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            $subject = $teacher->subjects()->where('subjects.id', $id)->firstOrFail();

            $subject->pivot->kkm = $request->kkm;

            $subject->pivot->save();

            return ['message' => 'success'];
        });
    });
});
