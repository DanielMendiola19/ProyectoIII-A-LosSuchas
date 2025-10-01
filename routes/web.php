<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

//SIGN UP
Route::get('/signup', [AuthController::class, 'showSignUpForm'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup');
Route::post('/signup/validate', [AuthController::class, 'validateField'])->name('signup.validate');

//LOGIN
Route::view('/login', 'login')->name('login.form');