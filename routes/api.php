<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\usercontroller;

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

route::post('register', [AuthController::class, 'signup']);
Route::post('login',[AuthController::class,'login']);

 Route::middleware(['auth:sanctum' , 'role:admin'])->group(function(){
 route::post('createmovie',[Admin::class,'createmovie']);
route::get('getmovie/{id}',[Admin::class,'getmovie']);
route::put('editmovie/{id}',[Admin::class,'updatemovie']);
route::delete('deletemovie/{id}',[Admin::class,'deletemovie']);
route::get('showtime',[Admin::class,'index']);
route::post('createshow',[Admin::class,'showcreate']);
route::put('updateshow/{id}',[Admin::class,'updateshowtime']);
route::delete('deleteshow/{id}',[Admin::class,'deleteshowtime']);
route::put('promoteUser/{id}',[Admin::class,'promoteUser']);
route::get('getreport',[Admin::class,'getreport']);



 });
 Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    route::get('listofshow/{date}',[usercontroller::class,'listOfMovieShowtime']);
    route::post('reserveSeats',[usercontroller::class,'reserveSeats']);
    route::get('listreservation',[usercontroller::class,'listReservation']);
    route::delete('cancelReservation/{id}',[usercontroller::class,'cancelReservation']);
 });


