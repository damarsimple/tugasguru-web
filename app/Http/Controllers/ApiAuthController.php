<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Form;
use App\Models\School;
use App\Models\StudentPpdb;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "password" => "required",
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->email)->first();

        $email = $user?->email;

        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $email)->firstOrFail();
            return response()->json([
                "user" => $user->load(
                    "classrooms",
                    "myclassrooms",
                    "school",
                    "followings",
                    "requestfollowers",
                    "city",
                    'followings'
                ),
                "token" => $user->createToken($user->name)->plainTextToken,
            ]);
        } else {
            return response()->json([
                'message' => 'Kredensial ini tidak ditemukan di catatan kami.',
            ]);
        }
    }

    public function register(Request $request)
    {
        $input = $request->toArray();

        $validationArray = [
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                Rule::unique(User::class),
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
            "province_id" => ["required"],
            "city_id" => ["required", "numeric"],
            "district_id" => ["required", "numeric"],
            "gender" => ["required", "numeric"],
            "phone" => ["required", "numeric",  Rule::unique(User::class)],
            "roles" => [
                "required",
                "in:TEACHER,STUDENT,STUDENT_PPDB,BIMBEL,GUARDIAN,GENERAL",
            ],
        ];

        switch ($input["roles"]) {
            case "BIMBEL":
                $validationArray["scanktp"] = ["required", "numeric"];
                $validationArray["noktp"] = ["required", "numeric"];
                break;
            case "TEACHER":
                $validationArray["school_id"] = ["required", "numeric"];
                $validationArray["npsn"] = ["required", "numeric", "in:" . School::find($input['school_id'])->npsn];
                break;
            case "STUDENT_PPDB":
                $validationArray["nisn"] = ["required", "numeric"];
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

        $user->save();

        switch ($user->roles) {
            case "TEACHER":
                $user->schools()->attach($input["school_id"]);
                break;
            case "BIMBEL":

                $form = new Form();

                $form->type = Form::REQUEST_TUTOR;

                $form->data = ['message' => 'REQUEST FROM REGISTER'];

                $user->forms()->save($form);

                $ktp = Attachment::find($input['scanktp']);

                $ktp->user_id = $user->id;
                $ktp->role = User::DOCUMENTS;
                $ktp->description = 'KTP';
                $ktp->attachable_id = $form->id;
                $ktp->attachable_type = Form::class;
                $ktp->save();

                $user->identity = [['type' => 'ktp', 'identifier' => $input['noktp']]];

                break;
            case "STUDENT":
                $user->nisn = $input["nisn"];
                $user->school_id = $input["school_id"];
                break;
            case "STUDENT_PPDB":
                $user->nisn = $input["nisn"];
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
                $client = new Client();
                $client->request('GET', $input['avatar'], ['sink' => public_path() . '/attachments/' . $attachment->name]);
            } catch (\Throwable $th) {
                return $user;
            }
        }

        event(new Registered($user));

        return response()->json([
            "user" => $user,
            "token" => $user->createToken($user->name)->plainTextToken,
        ]);
    }
    public function profile(Request $request)
    {
        $user = $request
            ->user()
            ->load(
                "classrooms",
                "myclassrooms",
                "school",
                "followings",
                "requestfollowers",
                "city"
            );

        return response()->json(["user" => $user]);
    }
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            "token" => $user->createToken($user->name)->plainTextToken,
        ]);
    }
}
