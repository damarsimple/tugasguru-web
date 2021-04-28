<?php

namespace App\Actions\Fortify;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $validationArray =  [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'province_id' => ['required'],
            'city_id' => ['required', 'numeric'],
            'district_id' => ['required', 'numeric'],
            'gender' => ['required', 'numeric'],
            'phone' => ['required', 'numeric'],
            'roles' => ['required', 'in:TEACHER,STUDENT,TEACHER_BIMBEL,GUARDIAN'],
        ];

        switch ($input['roles']) {
            case 'TEACHER_BIMBEL':
                break;
            case 'TEACHER':
                $validationArray['school_id'] = ['required', 'numeric'];
                break;
            case 'STUDENT':
                $validationArray['school_id'] = ['required', 'numeric'];
                $validationArray['nisn'] = ['required', 'numeric'];
                break;
            default:
                break;
        }

        Validator::make($input, $validationArray)->validate();

        $user =  User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'gender' => $input['gender'],
            'province_id' => $input['province_id'],
            'city_id' => $input['city_id'],
            'district_id' => $input['district_id'],
            'phone' => $input['phone'],
            'roles' => $input['roles']
        ]);

        switch ($input['roles']) {
            case 'TEACHER':
            case 'TEACHER_BIMBEL':
                $teacher = new Teacher();
                $teacher->user_id = $user->id;
                $teacher->specialty = $input['specialty'] ?? null;
                $teacher->academic_degree = $input['academic_degree'] ?? null;
                $teacher->is_bimbel = $input['roles'] == 'TEACHER_BIMBEL' ? true : false;
                $teacher->school_id = $input['school_id'] ?? null;
                $user->teacher()->save($teacher);
                break;
            case 'STUDENT':
                $student = new Student();
                $student->nisn = $input['nisn'];
                $student->school_id = $input['school_id'];
                $student->specialty = $input['specialty'] ?? null;
                $student->academic_degree = $input['academic_degree'] ?? null;
                $user->student()->save($student);
                break;
            default:
                break;
        }

        return $user;
    }
}
