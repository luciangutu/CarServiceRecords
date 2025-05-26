<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ServiceEntryController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Auth\GoogleController;



Route::get('/login', function () {
    return redirect()->route('google.login');
})->name('login');

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::resource('service-entries', ServiceEntryController::class);
    Route::resource('cars', CarController::class);

    Route::get('/', function () {
        return redirect()->route('service-entries.index');
    })->name('home');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});
