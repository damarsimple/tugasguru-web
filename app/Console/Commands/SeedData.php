<?php

namespace App\Console\Commands;

use App\Models\Assigment;
use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\District;
use App\Models\Exam;
use App\Models\Examtype;
use App\Models\Meeting;
use App\Models\Province;
use App\Models\Room;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Faker\Factory;
use Illuminate\Support\Facades\Hash;

use function Safe\file_get_contents;

class SeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:data {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate database with Indonesian data';

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
        $places = file_get_contents(base_path() . '/data/places.json');

        $places = json_decode($places);

        $onTest = $this->option('test');


        $examsType = [
            'Latihan',
            'PTS',
            'PAS',
            'UAS',
            'QUIZ',
            'Ulangan Harian',
            'Remedial'
        ];


        foreach ($examsType as $examstype) {
            $examtype = new Examtype();
            $examtype->name = $examstype;
            $examtype->save();
        }
        foreach ($places as $place) {
            $provinceName = $place->nama;
            if ($onTest && $provinceName !== "Kalimantan Timur")
                continue;
            $province = new Province();

            $province->name = $provinceName;

            $province->save();

            $cities = $place->cities;

            foreach ($cities as $city) {
                $districts = $city->districts;
                $city = $city->nama;

                if (str_contains($city, "Kabupaten")) {
                    $cityName = str_replace("Kabupaten ", "", $city);

                    $city = new City;

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
            "Prakarya"
        ];

        // $faker = new Factory();

        foreach ($subjectsData as $subjectdata) {
            $subject = new Subject();

            $subject->name = $subjectdata;

            $subject->save();
        }

        $subjectsIds = Subject::all()->map(fn ($e) => $e->id);

        $schoolTypeMap = [];

        $classTypeMap = [];

        foreach (glob(base_path() . '/data/schools/*.json') as $filename) {
            $schoolsData = file_get_contents($filename);

            $schoolsData = json_decode($schoolsData);

            // var_dump($schoolsData);

            foreach ($schoolsData as $key => $schools) {

                foreach ($schools as $key => $school) {

                    $disctrictName = preg_replace(['/Kec./', '/KEC. /', '/Kecamatan/'], "", $school->kecamatan);
                    if (str_contains($school->kabupaten_kota, "Kab.")) {
                        $cityName = str_replace("Kab. ", "Kabupaten ", $school->kabupaten_kota);
                    }

                    $provinceName = str_replace("Prov. ", "", $school->propinsi);
                    if ($onTest && $provinceName !== "Kalimantan Timur")
                        continue;

                    print($school->sekolah . PHP_EOL);
                    if (str_contains($provinceName, "D.K.I. ")) {
                        $provinceName = str_replace("D.K.I.", "DKI", $provinceName);
                    }
                    if (str_contains($provinceName, "D.I. ")) {
                        $provinceName = str_replace("D.I.", "DI", $provinceName);
                    }
                    try {
                        $provinceId = $provinceMap[$provinceName];

                        if (!array_key_exists($cityName, $cityMap)) {
                            $city = new City();
                            $city->name = preg_replace(['/Kota /', '/Kabupaten /'], "", $cityName);
                            $city->province_id = $provinceId;
                            $city->type = str_contains($cityName, 'Kabupaten') ? 'Kabupaten' : 'Kota';
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
                        if (!array_key_exists($school->bentuk, $schoolTypeMap)) {
                            $schooltype = new Schooltype();

                            switch (strtolower($school->bentuk)) {
                                case 'smk':
                                case 'sma':
                                    $schooltype->level = 3;
                                    break;

                                case 'smp':
                                    $schooltype->level = 2;
                                    break;
                                case 'sd':
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
                                case 'smk':
                                case 'sma':
                                    for ($i = 0; $i < 3; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 10;

                                        $classtypes[] = $classtype;
                                    }


                                    break;
                                case 'smp':
                                    for ($i = 0; $i < 3; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 7;

                                        $classtypes[] = $classtype;
                                    }
                                    break;
                                case 'sd':
                                    for ($i = 0; $i < 6; $i++) {
                                        $classtype = new Classtype();

                                        $classtype->level = $i + 1;

                                        $classtypes[] = $classtype;
                                    }
                                    break;
                                default:
                                    break;
                            }

                            $schooltype->classtypes()->saveMany($classtypes);
                        }

                        $schooltypeId = $schoolTypeMap[$school->bentuk];

                        $schoolModel = new School();

                        $schoolModel->name =  $school->sekolah;
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
                                case 'smk':
                                case 'sma':
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 10;
                                        $classtypesIds[] = $classTypeMap[$level];
                                    }
                                    break;
                                case 'smp':
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 7;
                                        $classtypesIds[] = $classTypeMap[$level];
                                    }
                                    break;
                                case 'sd':
                                    for ($i = 0; $i < 6; $i++) {
                                        $level = $i + 1;
                                        $classtypesIds[] = $classTypeMap[$level];
                                    }
                                    break;
                                default:
                                    break;
                            }
                        } catch (\Throwable $th) {
                            print($th->getMessage() . PHP_EOL);
                            switch (strtolower($school->bentuk)) {
                                case 'smk':
                                case 'sma':
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 10;
                                        $classTypeMap[$level] = Classtype::where('level', $level)->first()->id;
                                        $classtypesIds[] = $classTypeMap[$level];
                                    }
                                    break;
                                case 'smp':
                                    for ($i = 0; $i < 3; $i++) {
                                        $level = $i + 7;
                                        $classTypeMap[$level] = Classtype::where('level', $level)->first()->id;
                                        $classtypesIds[] = $classTypeMap[$level];
                                    }
                                    break;
                                case 'sd':
                                    for ($i = 0; $i < 6; $i++) {
                                        $level = $i + 1;
                                        $classTypeMap[$level] = Classtype::where('level', $level)->first()->id;
                                        $classtypesIds[] = $classTypeMap[$level];
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
                        print($schoolModel->id . PHP_EOL);
                    } catch (\Throwable $th) {
                        print($th->getMessage() . PHP_EOL);
                        print($th->getLine() . PHP_EOL);
                        continue;
                    }
                }
            }
        }



        $user = new User();
        $user->name = "Damar Albaribin Guru 1";
        $user->email = "damaralbaribin@gmail.com";
        $user->password = Hash::make("we5n9t5ReNV8gNE");
        $user->city_id = 1;
        $user->province_id = 1;
        $user->district_id = 1;
        $user->gender = 1;
        $user->phone = "08987181017";
        $user->roles = "TEACHER";

        $user->save();

        $teacher = new Teacher();
        $teacher->is_bimbel = false;
        $teacher->school_id = 1;
        $user->teacher()->save($teacher);

        $teacher->subjects()->attach(Subject::first());

        $user = new User();
        $user->name = "Damar Albaribin Guru 2";
        $user->email = "damaralbaribin2@gmail.com";
        $user->password = Hash::make("we5n9t5ReNV8gNE");
        $user->city_id = 1;
        $user->province_id = 1;
        $user->district_id = 1;
        $user->gender = 1;
        $user->phone = "08987181012";
        $user->roles = "TEACHER";

        $user->save();

        $teacher = new Teacher();
        $teacher->is_bimbel = false;
        $teacher->school_id = 1;
        $user->teacher()->save($teacher);


        $teacher->subjects()->attach(Subject::first());

        $user = new User();
        $user->name = "Damar Albaribin Siswa";
        $user->email = "damara1@gmail.com";
        $user->password = Hash::make("123456789");
        $user->city_id = 1;
        $user->province_id = 1;
        $user->district_id = 1;
        $user->gender = 1;
        $user->phone = "08987181014";
        $user->roles = "STUDENT";

        $user->save();

        $student = new Student();
        $student->nisn = 1234568123;
        $student->school_id = 1;
        $student->classtype_id = 1;
        $user->student()->save($student);


        $school = School::first();

        $classroom = new Classroom();
        $classroom->name =  "Test";
        $classroom->teacher_id = 1;
        $classroom->classtype_id = 1;
        $school->classrooms()->save($classroom);

        $classroom->students()->save($student);

        $classroom = new Classroom();
        $classroom->name =  "Test";
        $classroom->teacher_id = 2;
        $classroom->classtype_id = 1;
        $school->classrooms()->save($classroom);



        // $teacher->user->followingteachers()->attach($student->user->id);

        $classroom->students()->save($student);

        $meeting = new Meeting();

        $meeting->subject_id  = Subject::first()->id;

        $meeting->name = "Pertemuan oleh kian santang";

        $meeting->teacher_id = $teacher->id;

        $meeting->start_at = now();

        $classroom->meetings()->save($meeting);

        foreach (['Umum', 'Kelompok 1', 'Kelompok 2'] as $name) {
            $room = new Room();
            $room->name = $name;
            $room->identifier = "meeeting.chat." . $meeting->id;
            $meeting->rooms()->save($room);

            $room->users()->attach($teacher->user->id, ['is_administrator' => true]);

            $room->users()->attach($student->user->id);
        }

        $assigment = new Assigment();

        $assigment->name = "Test Assigment";

        $assigment->content = "Test";

        $assigment->classroom_id = $classroom->id;

        $assigment->subject_id = 1;

        $assigment->close_at = now()->addHour(4);

        $teacher->assigments()->save($assigment);

        print("finish at " . time() - $start . PHP_EOL);
        return 0;
    }
}
