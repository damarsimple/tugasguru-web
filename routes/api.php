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
use App\Models\Examtype;
use App\Models\Province;
use App\Models\Question;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\StudentAnswer;
use App\Models\Subject;
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



Route::group(['middleware' => ['auth:sanctum', EnsureStudent::class], 'prefix' => 'students'], function () {

    Route::group(['prefix' => 'classrooms'], function () {
        Route::get('/', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            return $student->classrooms()->with(
                'students.user',
                'teacher.user',
                'subject',
            )->get();
        });
        Route::post('/join', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;

            $student->classrooms()->attach(Classroom::findOrFail($request->classroom));

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
                'students.user.province',
                'teachers.user.province',
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
            foreach ($student->classrooms()->has('exams')->get() as $classroom) {
                $checkexam = $classroom->exams()
                    ->whereHas(
                        'examsessions',
                        fn ($e) => $e->where('close_at', '>', $now)
                    );

                if ($checkexam->exists()) {
                    foreach ($checkexam->get() as $exam) {
                        $events  = $events->merge($exam->examsessions()->with(
                            'exam.teacher.user',
                            'exam.subject'
                        )->get());
                    }
                }
            }

            return $events->all();
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
            if ($examresult = Examresult::where('examsession_id', $request->examsession)
                ->where('student_id', $student->id)
                ->where('exam_id', $exam->id)->exists()
            ) {
                return ['message' => 'already reported'];
            }

            $examresult = new Examresult();

            $examresult->examsession_id = $examsession->id;

            $examresult->start_at = now();

            $examresult->student_id = $student->id;

            $examresult->exam_id = $exam->id;

            $examresult->save();

            return ['message' => 'saved'];
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
                    'questions.answers',
                    'subject',
                    'supervisors.user',
                    'teacher.user',
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
                    'exam.teacher.user',
                    'exam.subject',
                    'exam.examtype',
                    'exam.supervisors.user',
                    'studentanswers.question.answers',
                    'studentanswers.answer',
                    'student',
                    'examsession'
                )
                ->firstOrFail();
        });


        Route::post('/submitanswer', function (Request $request) {
            /**  @var App/Models/Student $student  */
            $student = $request->user()->student;
            $examsession = Examsession::findOrFail($request->examsession);
            if (!now()->between($examsession->open_at, $examsession->close_at)) {
                return ['message' => 'session is over'];
            }

            if (!$examsession->token == $request->token) {
                return ['message' => 'wrong'];
            }

            $answer  = StudentAnswer::firstOrCreate([
                'question_id' => $request->question,
                'student_id' => $student->id,
                'exam_id' => $request->exam,
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
            $exam->code = $request->code;
            $exam->kkm = $request->kkm;
            $exam->hint = $request->hint;
            $exam->code = $request->code;
            $exam->description = $request->description;

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
                // Sat May 01 2021 01:44:50 GMT+0700 (Waktu Indonesia Barat)
                $examsession = new Examsession();
                $examsession->name = $examsessionData['name'];
                $examsession->open_at = Carbon::createFromFormat('D M d Y H:i:s e+',  $examsessionData['open_at']);
                $examsession->close_at = Carbon::createFromFormat('D M d Y H:i:s e+',  $examsessionData['close_at']);
                $examsession->token = $examsessionData['token'];
                $examsessions[] = $examsession;
            }

            $exam->examsessions()->saveMany($examsessions);
            $exam->questions()->attach($request->questions);
            $exam->classrooms()->attach($request->classrooms);

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
                'teachers.school',
                'students.school',
                'teachers.user.province',
                'students.user.province',
                'schooltype',
            )->get();
        });
    });

    Route::group(['prefix' => 'classrooms'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            if ($request->withExtra) {
                return $teacher->classrooms()->with(
                    'teacher.user',
                    'subject',
                    'students.user',
                )->get();
            } else {
                return $teacher->classrooms;
            }
        });

        Route::get('/all', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            if ($request->withExtra) {
                return $teacher->school->classrooms()->with(
                    'teacher.user',
                    'subject',
                    'students.user',
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
            $questions = (new Question())->with('classtypes', 'classtypes.schooltype', 'subjects', 'attachments', 'answers', 'answers.attachment');

            if ($request->classtypes) {
                $questions =  $questions->whereHas('classtypes', fn ($q) => $q->whereIn('classtype_id', $request->classtypes));
            }
            if ($request->subjects) {
                $questions =  $questions->whereHas('subjects', fn ($q) => $q->whereIn('subject_id', $request->subjects));
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



        Route::post('/create', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            foreach ($request->questions as $questionData) {
                $question = new Question();

                $question->content = $questionData['content'];

                $question->visibility = $request['visibility'];

                $question->type = $questionData['type'];

                $teacher->questions()->save($question);

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

                $question->subjects()->attach($request['subjects']);

                $question->classtypes()->attach($request['classtypes']);

                foreach (Attachment::whereIn('id', $questionData['attachments'])->get() as $attachment) {
                    $attachment->attachable()->associate($question)->save();
                }
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
    });
});
