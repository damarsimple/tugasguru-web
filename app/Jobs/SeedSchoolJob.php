<?php

namespace App\Jobs;

use App\Models\Classroom;
use App\Models\Classtype;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeedSchoolJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public $school,
        public $provinceId,
        public $cityId,
        public $districtId,
        public $schooltypeId,
        public $classTypeMap,
        public $subjectsIds,
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $school = $this->school;
        $provinceId = $this->provinceId;
        $cityId = $this->cityId;
        $districtId = $this->districtId;
        $schooltypeId = $this->schooltypeId;
        $subjectsIds = $this->subjectsIds;
        $classTypeMap = $this->classTypeMap;

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
    }
}
