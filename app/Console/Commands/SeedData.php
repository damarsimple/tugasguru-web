<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\Province;
use App\Models\School;
use App\Models\Schooltype;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Console\Command;
use Faker\Factory;


use function Safe\file_get_contents;

class SeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:data';

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
        $places = file_get_contents(base_path() . '/data/places.json');

        $places = json_decode($places);

        foreach ($places as $place) {
            $provinceName = $place->provinsi;

            $province = new Province();

            $province->name = $provinceName;

            $province->save();

            $cities = $place->kota;

            foreach ($cities as $city) {
                if (str_contains($city, "Kab.")) {
                    $cityName = str_replace("Kab. ", "", $city);

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

        $subjectsData = [
            "Pendidikan Agama",
            "Pendidikan Kewarganegaraan",
            "Bahasa Indonesia, Matematika",
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
            $schools = file_get_contents($filename);

            $schools = json_decode($schools);

            foreach ($schools as $school) {
                print($school->sekolah . PHP_EOL);

                if (str_contains($school->kabupaten_kota, "Kab.")) {
                    $cityName = str_replace("Kab. ", "Kabupaten ", $school->kabupaten_kota);
                }

                $provinceName = str_replace("Prov. ", "", $school->propinsi);

                try {

                    $provinceId = $provinceMap[$provinceName];
                    $cityId = $cityMap[$cityName];

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
                                break;
                        }

                        if (!$schooltype->level) {
                            continue;
                        }
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

                    $classrooms = [];
                    foreach ($schoolModel->classtypes as $classtype) {
                        for ($i = 0; $i < 3; $i++) {
                            $classroom = new Classroom();
                            $classroom->classtype_id = $classtype->id;
                            $classroom->name = "Kelas " . $classtype->level . " " . chr($i + 65);
                            $classrooms[] = $classroom;
                        }
                    }

                    $schoolModel->classrooms()->saveMany($classrooms);
                    print($schoolModel->id . PHP_EOL);
                } catch (\Throwable $th) {
                    print($th->getMessage() . PHP_EOL);
                    continue;
                }
            }
        }





        return 0;
    }
}
