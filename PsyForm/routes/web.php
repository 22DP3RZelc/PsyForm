<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/home', function () {
    return view('home');
})->name('home');

Route::get('/register', function () {
    return view('register');
})->name('register');

