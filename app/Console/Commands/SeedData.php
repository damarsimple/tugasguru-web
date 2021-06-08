<?php

namespace App\Console\Commands;

use App\Enum\Ability;
use App\Models\Absent;
use App\Models\Agenda;
use App\Models\Answer;
use App\Models\Assigment;
use App\Models\Attendance;
use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\District;
use App\Models\Exam;
use App\Models\Examresult;
use App\Models\Examsession;
use App\Models\Examtype;
use App\Models\Meeting;
use App\Models\Packagequestion;
use App\Models\Province;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Reward;
use App\Models\Room;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\StudentAnswer;
use App\Models\StudentAssigment;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "seed:data {--test}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Populate database with Indonesian data";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = time();
        $places = file_get_contents(base_path() . "/data/places.json");

        $places = json_decode($places);

        $onTest = $this->option("test");

        $examsType = ["Latihan", "Ulangan Harian", "PTS", "PAS"];

        foreach ($examsType as $examstype) {
            $examtype = new Examtype();
            $examtype->name = $examstype;
            $examtype->save();
        }
        foreach ($places as $place) {
            $provinceName = $place->nama;
            if ($onTest && $provinceName !== "Kalimantan Timur") {
                continue;
            }
            $province = new Province();

            $province->name = $provinceName;

            $province->save();

            $cities = $place->cities;

            foreach ($cities as $city) {
                $districts = $city->districts;
                $city = $city->nama;

                if (str_contains($city, "Kabupaten")) {
                    $cityName = str_replace("Kabupaten ", "", $city);

                    $city = new City();

                    $city->province_id = $province->id;

                    $city->name = $cityName;

                    $city->type = "Kabupaten";

                    $city->save();
                } else {
                    $cityName = str_replace("Kota ", "", $city);

                    $city = new City();

                    $city->province_id = $province->id;

                    $city->name = $cityName;

                    $city->type = "Kota";

                    $city->save();
                }

                $districtsModel = [];

                foreach ($districts as $disctrictBase) {
                    $district = new District();
                    $district->name = $disctrictBase->nama;
                    $districtsModel[] = $district;
                }
                $city->districts()->saveMany($districtsModel);
            }
        }

        $provinceMap = [];

        foreach (Province::all() as $province) {
            $provinceMap[$province->name] = $province->id;
        }

        $cityMap = [];

        foreach (City::all() as $city) {
            $cityMap[$city->type . " " . $city->name] = $city->id;
        }

        $districtMap = [];

        foreach (District::all() as $district) {
            $districtMap[$district->name] = $district->id;
        }

        $subjectsData = [
            "Pendidikan Agama",
            "Pendidikan Kewarganegaraan",
            "Bahasa Indonesia",
            "Matematika",
            "Ilmu Pengetahuan Alam",
            "Ilmu Pengetahuan Sosial",
            "Bahasa Inggris",
            "Seni Budaya",
            "Pendidikan Jasmani",
            "Prakarya",
        ];

        // $faker = new Factory();

        foreach ($subjectsData as $subjectdata) {
            $subject = new Subject();

            $subject->name = $subjectdata;

            $subject->save();
        }

        $subjectsIds = Subject::all()->map(fn($e) => $e->id);

        foreach (["Komedi", "Penalaran Umum"] as $subjectdata) {
            $subject = new Subject();

            $subject->name = $subjectdata;
            $subject->type = "QUIZ";
            $subject->save();
        }

        $schoolTypeMap = [];

        $classTypeMap = [];

        foreach (glob(base_path() . "/data/schools/*.json") as $filename) {
            $schoolsData = file_get_contents($filename);

            $schoolsData = json_decode($schoolsData);

            // var_dump($schoolsData);

            foreach ($schoolsData as $key => $schools) {
                foreach ($schools as $key => $school) {
                    if (str_contains($school->sekolah, "SD")) {
                        continue;
                    }
                    if (str_contains($school->sekolah, "SMP")) {
                        continue;
                    }
                    if (str_contains($school->sekolah, "SMA")) {
                        continue;
                    }

                    $disctrictName = preg_replace(
                        ["/Kec./", "/KEC. /", "/Kecamatan/"],
                        "",
                        $school->kecamatan
                    );
                    if (str_contains($school->kabupaten_kota, "Kab.")) {
                        $cityName = str_replace(
                            "Kab. ",
                            "Kabupaten ",
                            $school->kabupaten_kota
                        );
                    }

                    $provinceName = str_replace(
                        "Prov. ",
                        "",
                        $school->propinsi
                    );
                    if ($onTest && $provinceName !== "Kalimantan Timur") {
                        continue;
                    }

                    print $school->sekolah . PHP_EOL;
                    if (str_contains($provinceName, "D.K.I. ")) {
                        $provinceName = str_replace(
                            "D.K.I.",
                            "DKI",
                            $provinceName
                        );
                    }
                    if (str_contains($provinceName, "D.I. ")) {
                        $provinceName = str_replace(
                            "D.I.",
                            "DI",
                            $provinceName
                        );
                    }
                    try {
                        $provinceId = $provinceMap[$provinceName];

                        if (!array_key_exists($cityName, $cityMap)) {
                            $city = new City();
                            $city->name = preg_replace(
                                ["/Kota /", "/Kabupaten /"],
                                "",
                                $cityName
                            );
                            $city->province_id = $provinceId;
                            $city->type = str_contains($cityName, "Kabupaten")
                                ? "Kabupaten"
                                : "Kota";
                            $city->save();
                            $cityMap[$cityName] = $city->id;
                        }

                        $cityId = $cityMap[$cityName];
                        if (!array_key_exists($disctrictName, $districtMap)) {
                            $district = new District();
                            $district->name = $disctrictName;
                            $district->city_id = $cityId;
                            $district->save();
                            $districtMap[$disctrictName] = $district->id;
                        }

                        $districtId = $districtMap[$disctrictName];
                        if (
                            !array_key_exists($school->bentuk, $schoolTypeMap)
                        ) {
                            $schooltype = new Schooltype();

                            switch (strtolower($school->bentuk)) {
                                case "smk":
                                case "sma":
                                    $schooltype->level = 3;
                                    break;

                                case "smp":
                                    $schooltype->level = 2;
                                    break;
                                case "sd":
                                    $schooltype->level = 1;
                                    break;
                                default:
                                    $schooltype->level = 4;
                                    break;
                            }

                            // if (!$schooltype->level) {
                            //     continue;
                            // }
                            $schooltype->name = $school->bentuk;

                            $schooltype->save();

                            $schoolTypeMap[$schooltype->name] = $schooltype->id;

                            $classtypes = [];

                            switch (strtolower($school->bentuk)) {
                                case "smk":
                                case "sma":
                                    for ($i = 0; $i < 3; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 10;

                                        $classtypes[] = $classtype;
                                    }

                                    break;
                                case "smp":
                                    for ($i = 0; $i < 3; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 7;

                                        $classtypes[] = $classtype;
                                    }
                                    break;
                                case "slb":
                                case "sd":
                                    for ($i = 0; $i < 6; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 1;

                                        $classtypes[] = $classtype;
                                    }
                                    break;
                                default:
                                    print strtolower($school->bentuk) . PHP_EOL;
                                    exit();
                                    break;
                            }

                            $schooltype->classtypes()->saveMany($classtypes);
                        }

                        $schooltypeId = $schoolTypeMap[$school->bentuk];

                        $schoolModel = new School();

                        $schoolModel->name = $school->sekolah;
                        $schoolModel->province_id = $provinceId;
                        $schoolModel->npsn = $school->npsn;
                        $schoolModel->city_id = $cityId;
                        $schoolModel->district_id = $districtId;
                        $schoolModel->address = $school->alamat_jalan;
                        $schoolModel->schooltype_id = $schooltypeId;
                        $schoolModel->latitude = $school->lintang;
                        $schoolModel->longtitude = $school->bujur;

                        $schoolModel->save();

                        $schoolModel->subjects()->attach($subjectsIds);

                        $classtypesIds = [];

                        try {
                            switch (strtolower($school->bentuk)) {
                                case "smk":
                                case "sma":
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 10;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                case "smp":
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 7;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                case "sd":
                                    for ($i = 0; $i < 6; $i++) {
                                        $level = $i + 1;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                default:
                                    break;
                            }
                        } catch (\Throwable $th) {
                            print $th->getMessage() . PHP_EOL;
                            switch (strtolower($school->bentuk)) {
                                case "smk":
                                case "sma":
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 10;
                                        $classTypeMap[
                                            $level
                                        ] = Classtype::where(
                                            "level",
                                            $level
                                        )->first()->id;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                case "smp":
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 7;
                                        $classTypeMap[
                                            $level
                                        ] = Classtype::where(
                                            "level",
                                            $level
                                        )->first()->id;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                case "sd":
                                    for ($i = 0; $i < 6; $i++) {
                                        $level = $i + 1;
                                        $classTypeMap[
                                            $level
                                        ] = Classtype::where(
                                            "level",
                                            $level
                                        )->first()->id;
                                        $classtypesIds[] =
                                            $classTypeMap[$level];
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }

                        $schoolModel->classtypes()->attach($classtypesIds);

                        // $classrooms = [];
                        // foreach ($schoolModel->classtypes as $classtype) {
                        //     for ($i = 0; $i < 3; $i++) {
                        //         $classroom = new Classroom();
                        //         $classroom->classtype_id = $classtype->id;
                        //         $classroom->name = "Kelas " . $classtype->level . " " . chr($i + 65);
                        //         $classrooms[] = $classroom;
                        //     }
                        // }

                        // $schoolModel->classrooms()->saveMany($classrooms);
                        print $schoolModel->id . PHP_EOL;
                    } catch (\Throwable $th) {
                        print $th->getMessage() . PHP_EOL;
                        print $th->getLine() . PHP_EOL;
                        continue;
                    }
                }
            }
        }

        if ($onTest) {
            $teacher = new User();
            $teacher->name = "Damar Albaribin Guru 1";
            $teacher->email = "damaralbaribin@gmail.com";
            $teacher->password = Hash::make("we5n9t5ReNV8gNE");
            $teacher->city_id = 1;
            $teacher->province_id = 1;
            $teacher->district_id = 1;
            $teacher->access = [Ability::GRADE_REPORT];
            $teacher->gender = 1;
            $teacher->phone = "08987181017";
            $teacher->roles = "TEACHER";

            $teacher->save();

            $teacher->schools()->attach(1);

            $teacher->is_bimbel = false;

            $teacher->subjects()->attach(Subject::first());

            $secondteacher = new User();
            $secondteacher->name = "Damar Albaribin Guru 2";
            $secondteacher->email = "damaralbaribin2@gmail.com";
            $secondteacher->password = Hash::make("we5n9t5ReNV8gNE");
            $secondteacher->city_id = 1;
            $secondteacher->province_id = 1;
            $secondteacher->district_id = 1;
            $secondteacher->gender = 1;
            $secondteacher->phone = "08987181012";
            $secondteacher->roles = "TEACHER";

            $secondteacher->save();

            $secondteacher->schools()->attach(1);

            $secondteacher->is_bimbel = false;
            $secondteacher->school_id = 1;

            $secondteacher->subjects()->attach(Subject::first());

            $student = new User();
            $student->name = "Damar Albaribin Siswa";
            $student->email = "damara1@gmail.com";
            $student->password = Hash::make("123456789");
            $student->city_id = 1;
            $student->school_id = 1;
            $student->province_id = 1;
            $student->district_id = 1;
            $student->gender = 1;
            $student->phone = "08987181014";
            $student->roles = "STUDENT";

            $student->nisn = 1234568123;
            $student->school_id = 1;
            $student->classtype_id = 1;

            $student->save();

            $secondstudent = new User();
            $secondstudent->name = "Damar Albaribin Siswa 2";
            $secondstudent->email = "damara2@gmail.com";
            $secondstudent->password = Hash::make("123456789");
            $secondstudent->city_id = 1;
            $secondstudent->school_id = 1;
            $secondstudent->province_id = 1;
            $secondstudent->district_id = 1;
            $secondstudent->gender = 1;
            $secondstudent->phone = "08987181015";
            $secondstudent->roles = "STUDENT";

            $secondstudent->nisn = 1234568123;
            $secondstudent->school_id = 1;
            $secondstudent->classtype_id = 1;

            $secondstudent->save();

            $studentIds = [$student->id, $secondstudent->id];

            $school = School::first();

            $firstclassroom = new Classroom();
            $firstclassroom->name = "Test Pertama ";
            $firstclassroom->teacher_id = 1;
            $firstclassroom->classtype_id = 1;
            $school->classrooms()->save($firstclassroom);

            $firstclassroom->students()->attach($studentIds);

            $classroom = new Classroom();
            $classroom->name = "Test Kedua ";
            $classroom->teacher_id = 1;
            $classroom->classtype_id = 1;
            $school->classrooms()->save($classroom);

            $classroom->students()->attach($studentIds);

            $absent = new Absent();

            $absent->teacher_id = $teacher->id;
            $absent->type = "IZIN";
            $absent->reason = "test";
            $absent->start_at = now();
            $absent->finish_at = now()->addDay(1);

            $student->absents()->save($absent);

            $meeting = new Meeting();

            $meeting->subject_id = Subject::first()->id;

            $meeting->name = "Pertemuan oleh kian santang";

            $meeting->teacher_id = $teacher->id;

            $meeting->start_at = now();

            $firstclassroom->meetings()->save($meeting);

            $subscription = new Subscription();
            $subscription->name = "Guru Plus 1 Tahun";
            $subscription->duration = 12 * 30;
            $subscription->price = 50000;
            $subscription->ability = json_encode([
                Ability::ABSENT_CONSULT,
                Ability::GRADE_REPORT,
            ]);

            $subscription->save();

            $subscription = new Subscription();
            $subscription->name = "Wali Kelas 1 Tahun";
            $subscription->duration = 12 * 30;
            $subscription->price = 50000;
            $subscription->ability = json_encode([Ability::HOMEROOM]);

            $subscription->save();

            $subscription = new Subscription();
            $subscription->name = "Guru BK 1 Tahun";
            $subscription->duration = 12 * 30;
            $subscription->price = 50000;
            $subscription->ability = json_encode([Ability::COUNSELING]);

            $subscription->save();

            $subscription = new Subscription();
            $subscription->name = "Kepala Sekolah 1 Tahun";
            $subscription->duration = 12 * 30;
            $subscription->price = 50000;
            $subscription->ability = json_encode([Ability::HEADMASTER]);

            $subscription->save();

            // $transaction = new Transaction();

            // $transaction->amount = $subscription->price;
            // $transaction->payment_method = Transaction::XENDIT;

            // $transaction->transactionable_id = $subscription->id;
            // $transaction->transactionable_type = $subscription::class;
            // $transaction->uuid = Str::uuid();

            // try {
            //     $model = app($transaction->transactionable_type)->findOrFail($transaction->transactionable_id);
            // } catch (\Throwable $th) {
            //     return ['message' => 'model invalid'];
            // }

            // $transaction->description = 'Pembelian ' . $model->name . ' sebesar ' . $transaction->amount;

            // $invoice = Xendit::makePayment(
            //     $transaction->amount,
            //     $transaction->uuid,
            //     $transaction->description,
            //     $teacher->email
            // );
            // $transaction->invoice_request = json_encode($invoice);

            // $transaction->staging_url = $invoice['invoice_url'];

            // $teacher->transactions()->save($transaction);

            foreach (["Umum", "Kelompok 1", "Kelompok 2"] as $name) {
                $room = new Room();
                $room->name = $name;
                $room->identifier = "meeting.chat." . $meeting->id;
                $meeting->rooms()->save($room);

                $room
                    ->users()
                    ->attach($teacher->id, ["is_administrator" => true]);

                $room->users()->attach($studentIds);
            }

            $rand = mt_rand(6, 10);

            for ($i = 0; $i < $rand; $i++) {
                $assigment = new Assigment();
                $assigment->name = "Test Assigment";
                $assigment->content = "Test";
                $assigment->classroom_id = $classroom->id;
                $assigment->subject_id = 1;
                $assigment->close_at = now()->addHour(4);

                $teacher->assigments()->save($assigment);

                foreach ($studentIds as $x) {
                    $studentassigment = new StudentAssigment();
                    $studentassigment->user_id = $x;
                    $studentassigment->content = "aaa";
                    $studentassigment->grade = mt_rand(40, 100);
                    $studentassigment->is_graded = true;
                    $studentassigment->comment = "Yes";
                    $studentassigment->turned_at = now();
                    $assigment->studentassigments()->save($studentassigment);
                }
                print "assigment $i\n";
            }

            $packagequestion = new Packagequestion();

            $packagequestion->name = "TEST 1";
            $packagequestion->classtype_id = Classtype::first()->id;
            $packagequestion->subject_id = Subject::first()->id;
            $teacher->packagequestions()->save($packagequestion);

            for ($i = 0; $i < $rand; $i++) {
                $question = new Question();
                $question->user_id = $teacher->id;
                $question->classtype_id = $packagequestion->classtype_id;
                $question->subject_id = $packagequestion->subject_id;
                $question->type = Question::MULTI_CHOICE;
                $question->content = "Yes PG Test " . $i + 1;

                $packagequestion->questions()->save($question);

                for ($j = 0; $j < 4; $j++) {
                    $answer = new Answer();
                    $answer->content = "Yes Answer " . $j + 1;
                    $answer->is_correct = $j == 0;
                    $question->answers()->save($answer);
                    print "answer $j\n";
                }

                print "question $i\n";
            }

            for ($i = 0; $i < $rand; $i++) {
                $question = new Question();
                $question->user_id = $teacher->id;
                $question->classtype_id = $packagequestion->classtype_id;
                $question->subject_id = $packagequestion->subject_id;
                $question->type =
                    $i % 2 == 0 ? Question::ESSAY : Question::FILLER;
                $question->content = "Yes ESSAY / FILLER Test " . $i + 1;

                $packagequestion->questions()->save($question);

                $answer = new Answer();
                $answer->content = "Yes Answer " . $i + 1;
                $answer->is_correct = true;
                $question->answers()->save($answer);

                print "question $i\n";
            }

            $questions = Question::all();
            $questionIds = $questions->pluck("id");
            foreach (Examtype::all() as $examtype) {
                for ($i = 0; $i < $rand; $i++) {
                    $exam = new Exam();

                    $exam->name = "Test Exam $i";
                    $exam->teacher_id = $teacher->id;
                    $exam->description = " test ";
                    $exam->hint = "11";
                    $exam->subject_id = 1;
                    $exam->is_odd_semester = true;
                    $exam->education_year_start = 2020;
                    $exam->education_year_end = 2021;
                    $exam->examtype_id = $examtype->id;
                    $classroom->exams()->save($exam);

                    $exam->questions()->attach($questionIds);

                    $examsession = new Examsession();

                    $examsession->name = "Test 1";
                    $examsession->open_at = now();
                    $examsession->close_at = now()->addHour(1);
                    $exam->examsessions()->save($examsession);

                    foreach ($studentIds as $x) {
                        $user = User::find($x);

                        $examresult = new Examresult();
                        $examresult->examsession_id = $examsession->id;
                        $examresult->exam_id = $exam->id;
                        $examresult->start_at = now();
                        $examresult->finish_at = now()->addHour(1);
                        $examresult->grade = mt_rand(60, 100);
                        $examresult->is_proccessed = true;
                        $user->examresults()->save($examresult);

                        foreach ($questions as $v => $y) {
                            $studentanswer = new StudentAnswer();
                            $studentanswer->answer_id =
                                $v % 2 == 0
                                    ? $y->correctanswer->id
                                    : $y
                                        ->answers()
                                        ->pluck("id")
                                        ->random();
                            $studentanswer->examsession_id = $examsession->id;
                            $studentanswer->exam_id = $exam->id;
                            $studentanswer->question_id = $y->id;
                            $studentanswer->examresult_id = $examresult->id;

                            $user->studentanswers()->save($studentanswer);
                        }
                    }
                    print "exam $i\n";

                    if ($examtype->name == "PAS" || $examtype->name == "PTS") {
                        break;
                    }
                }
            }

            $teacher->assigments()->save($assigment);

            $teacher
                ->followers()
                ->attach([$student->id => ["is_accepted" => true]]);
            $teacher
                ->followers()
                ->attach([$secondstudent->id => ["is_accepted" => true]]);

            $quiz = new Quiz();
            $quiz->subject_id = 1;
            $quiz->name = "TEST QUIZ";
            $quiz->description = "TEST QUIZ";
            $quiz->visibility = "PUBLIK";

            $user->quizzes()->save($quiz);
            $quiz->questions()->attach([1]);

            $voucher = new Voucher();
            $voucher->name = "test voucher";
            $voucher->code = "test";
            $voucher->percentage = 0.69;
            $voucher->description = "test voucher";
            $voucher->expired_at = now()->addDay(1);
            $voucher->save();

            $reward = new Reward();
            $reward->name = "test reward";
            $reward->prize_pool = 100.0;
            // ^total hadiah
            $reward->reward = 100.0;
            // ^hadiah per pencapaian
            $reward->is_active = true;
            $reward->description = "test reward";
            $reward->minimum_play_count = 1;
            // ^minimum orang bermain untuk mendapatkan hadiah
            $reward->save();

            $agenda = new Agenda();
            $agenda->name = "test";
            $agenda->description = "test reward";
            $agenda->finish_at = now()->addHour(2);
            $teacher->agendas()->save($agenda);

            foreach (School::first()->teachers->pluck("id") as $id) {
                Attendance::firstOrCreate([
                    "user_id" => $id,
                    "attendable_id" => $agenda->id,
                    "attendable_type" => Agenda::class,
                ]);
            }
        }

        print "finish at " . time() - $start . PHP_EOL;
        return 0;
    }
}
