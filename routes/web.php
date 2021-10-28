<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

use App\Http\Controllers\FCMController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('Public/home');
})->name('home');
*/
// Route::get('/', function () {
//     return view('index');
// })->name('failed-verify');
// Route::get('/well', function () {
//     return view('welcome');
// })->name('failed-verify');
// Route::get('/fcm',[FCMController::class,'index']);
// Route::get('/fcm/send',[FCMController::class,'sendNotification']);

Route::get('app/success-verify', function () {
    return view('Template/success-verify');
})->name('success-verify');

Route::get('fcm', function () {
    return view('fcm');
})->name('success-verify');
/*
Route::get('app/register', function () {
    return view('Auth/register');
})->name('register');

Route::post('app/register', [RegisterController::class,'webRequest'])->name('register');

Route::get('app/login', function () {
    return view('Auth/login');
})->name('login');

Route::post('app/login', [LoginController::class,'webRequest'])->name('login');

Route::get('app/forgot-password', function () {
    return view('Auth/forgot-password');
})->name('forgot-password');

Route::post('app/forgot-password', [ForgotPasswordController::class,'webRequest'])->name('forgot-password');

Route::get('app/change-password', function () {
    return view('Auth/change-password');
})->name('change-password');

Route::post('app/change-password', [ChangePasswordController::class,'webRequest'])->name('change-password');

#Route::get('api/register', function () {
#    return view('welcome');
#});

Route::get('form', function () {
    return view('Auth/dumy');
});*/