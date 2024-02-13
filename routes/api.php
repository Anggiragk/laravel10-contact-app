<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
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

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::get('users/current', [UserController::class, 'get']);
    Route::patch('users/current', [UserController::class, 'update']);
    Route::delete('users/logout', [UserController::class, 'logout']);

    Route::get('/contacts', [ContactController::class, 'search']);
    Route::post('/contacts', [ContactController::class, 'create']);
    Route::get('/contacts/{contactId}', [ContactController::class, 'get'])->where("contactId" , "[0-9]+");
    Route::put('/contacts/{contactId}', [ContactController::class, 'update'])->where("contactId" , "[0-9]+");
    Route::delete('/contacts/{contactId}', [ContactController::class, 'delete'])->where("contactId" , "[0-9]+");

    Route::post('/contacts/{idContact}/addresses', [AddressController::class, 'create'])->where("idContact" , "[0-9]+");
    Route::get('/contacts/{idContact}/addresses', [AddressController::class, 'list'])->where("idContact" , "[0-9]+");
    Route::get('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'get'])
    ->where("idContact" , "[0-9]+")
    ->where("idAddress" , "[0-9]+");
    Route::put('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'update'])
    ->where("idContact" , "[0-9]+")
    ->where("idAddress" , "[0-9]+");
    Route::delete('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'delete'])
    ->where("idContact" , "[0-9]+")
    ->where("idAddress" , "[0-9]+");
});
