<?php

namespace App\Actions\Fortify;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

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
        $validationArray = [
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                Rule::unique(User::class),
            ],
            "password" => $this->passwordRules(),
            "province_id" => ["required"],
            "city_id" => ["required", "numeric"],
            "district_id" => ["required", "numeric"],
            "gender" => ["required", "numeric"],
            "phone" => ["required", "numeric"],
            "roles" => [
                "required",
                "in:TEACHER,STUDENT,TEACHER_BIMBEL,GUARDIAN",
            ],
        ];

        switch ($input["roles"]) {
            case "TEACHER_BIMBEL":
                break;
            case "TEACHER":
                $validationArray["school_id"] = ["required", "numeric"];
                break;
            case "STUDENT":
                $validationArray["school_id"] = ["required", "numeric"];
                $validationArray["nisn"] = ["required", "numeric"];
                break;
            default:
                break;
        }

        Validator::make($input, $validationArray)->validate();

        $user = User::create([
            "name" => $input["name"],
            "email" => $input["email"],
            "password" => Hash::make($input["password"]),
            "gender" => $input["gender"],
            "province_id" => $input["province_id"],
            "city_id" => $input["city_id"],
            "district_id" => $input["district_id"],
            "phone" => $input["phone"],
            "specialty" => $input["specialty"] ?? null,
            "academic_degree" => $input["academic_degree"] ?? null,
            "roles" => $input["roles"],
        ]);

        switch ($input["roles"]) {
            case "TEACHER":
                $user->schools()->attach($input["school_id"]);
            case "TEACHER_BIMBEL":
                $user->is_bimbel =
                    $input["roles"] == "TEACHER_BIMBEL" ? true : false;
                break;
            case "STUDENT":
                $user->nisn = $input["nisn"];
                $user->school_id = $input["school_id"];
                break;
            default:
                break;
        }

        $user->save();


        if (array_key_exists('avatar', $input)) {
            try {
                $attachment = new Attachment();

                $attachment->name = Str::uuid() . "." . ".jpg";

                $attachment->mime =  "image/jpeg";
                $attachment->is_proccessed = false;
                $attachment->original_size = 0;
                $attachment->compressed_size = 0;
                $attachment->role = User::PROFILEPICTURE;
                $user->attachments()->save($attachment);

                // $file->move('attachments', $attachment->name);
                $client = new Client();
                $client->request('GET', $input['avatar'], ['sink' => public_path() . '/attachments/' . $attachment->name]);
            } catch (\Throwable $th) {
                return $user;
            }
        }

        return $user;
    }
}
