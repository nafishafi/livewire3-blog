<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [UserController::class, 'homePage']);

Route::get('/contact/page', [UserController::class, 'ContactPage']);

Route::get('/registration/form', [AuthController::class, 'loadRegisterForm']);

Route::post('/register/user', [AuthController::class, 'registerUser'])->name('registerUser');   

Route::get('/login/form', [AuthController::class, 'loadLoginPage']);

Route::post('/login/user', [AuthController::class, 'LoginUser'])->name('LoginUser');

Route::get('/home',[AuthController::class, 'loadHomePage']);

Route::get('/logout',[AuthController::class, 'LogoutUser']);