<?php

use App\Http\Controllers\ApiAuthController;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Schooltype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/provinces/{id}/city', fn ($id) => Province::find($id)->cities);
Route::get('/cities/{id}/districts', fn ($id) => City::find($id)->districts);
Route::get('/cities/{id}/schools', function (Request $request, $id) {
    $city =  City::find($id);

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
