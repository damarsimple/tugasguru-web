<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);
        $credentials = $request->only("email", "password");
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->firstOrFail();
            return response()->json([
                "user" => $user,
                "token" => $user->createToken($user->name)->plainTextToken,
            ]);
        } else {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ]);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required",
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
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
                "requestfollowings",
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
