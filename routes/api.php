<?php

use App\Actions\Attachment\Upload;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\EnsureTeacher;
use App\Models\Answer;
use App\Models\Attachment;
use App\Models\City;
use App\Models\Classroom;
use App\Models\ClassroomTeacherSubject;
use App\Models\Classtype;
use App\Models\District;
use App\Models\Exam;
use App\Models\Examsession;
use App\Models\Examtype;
use App\Models\Province;
use App\Models\Question;
use App\Models\School;
use App\Models\Schooltype;
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



Route::group(['middleware' => ['auth:sanctum', EnsureTeacher::class], 'prefix' => 'teachers'], function () {



    Route::get('/finishinit', function (Request $request) {
        /**  @var App/Models/Teacher $teacher  */
        $teacher = $request->user()->teacher;

        $teacher->is_init = true;

        $teacher->save();
        return response('finish', 200);
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
            $exam->classrooms()->attach($request->questions);
            $exam->subjects()->attach($request->questions);

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



    Route::group(['prefix' => 'classroomteachersubjects'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return $teacher->classroomteachersubjects()->with(
                'subject',
                'classroom',
                'teacher.user'
            )->get();
        });
        Route::post('/add', function (Request $request) {


            if (!$request->subject_id || !$request->classroom_id)
                return response('invalid', 422);

            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            return ClassroomTeacherSubject::firstOrCreate([
                'teacher_id' => $teacher->id,
                'subject_id' => $request->subject_id,
                'classroom_id' => $request->classroom_id,
            ]);
        });
    });

    Route::group(['prefix' => 'classrooms'], function () {

        Route::get('/', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            return $teacher
                ->classrooms()
                ->with(
                    'homeroomteacher.user',
                    'classroomteachersubjects.teacher.user',
                    'classroomteachersubjects.subject',
                    'students',
                )->get();
        });

        Route::get('/all', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            if ($request->withExtra) {
                return $teacher->school->classrooms()->with(
                    'classroomteachersubjects.teacher',
                    'classroomteachersubjects.subject',
                    'students',
                    'homeroomteacher'
                )->get();
            } else {
                return $teacher->school->classrooms;
            }
        });

        Route::get('/nohomeroom', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;

            return $teacher->school->classrooms()->whereNull('homeroom_id')->get();
        });

        Route::post('/subject/add', function (Request $request) {
            /**  @var App/Models/Teacher $teacher  */
            $teacher = $request->user()->teacher;
            /**  @var App/Models/School $school  */
            $school = $teacher->school;

            $classroom = new Classroom();
            $classroom->name = "Kelas " . $request->classtype_level . " " .  $request->name;
            $classroom->classtype_id = $request->classtype_id;
            $classroom->homeroom_id = $teacher->id;
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
                    $answer->is_correct = $i == $questionData['correctanswer'];
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
