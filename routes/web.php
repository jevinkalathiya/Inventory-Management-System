<?php

use App\Http\Middleware\SendOtp;
use App\Http\Middleware\ValidUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\Api_Forwarder\ApiForwarderController;

Route::middleware('guest')->group(function (){ // only non login users can access this
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'LoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'RegisterForm')->name('register');
        Route::post('/register', 'register');
        Route::get('/mfa', 'MfaForm')->name('mfa');
        Route::post('/mfa', 'mfa')->name('mfa');
        Route::get('/resendotp','sendMail')->name('sendMail')->middleware(SendOtp::class);
    });
});

Route::middleware([ValidUser::class])->group(function () { // only for logged in user
    Route::get('/', function () {
        return view('index');
    })->name('index');
    Route::get('/list/{type}', function (string $type) {
        return view('list', ['type' => $type]);
    })->where('type', 'category|product')->name('list');
    Route::get('/user', function () {
        return view('user');
    })->name('user');

    // For category & product
    Route::controller(CategoryProductController::class)->group(function () {
        Route::get('/list/{type}', 'showList')->where('type', 'category|product')->name('list');
    });
    
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // Api request forwarder ('wa1' is short for 'web api v1')
    Route::match(['get', 'post', 'put', 'delete'], '/wa1/{endpoint}', [ApiForwarderController::class, 'ApiForwarder'])->where('endpoint', '.*');

});

Route::get('/session', function(){
    $value = session()->all();
    echo "<pre>";
    print_r($value);
    echo "</pre>";
});

