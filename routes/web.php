<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('pages.login');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

Route::get('/admin', function () {
    return view('pages.admin_review');
});

Route::get('/admin/review', function () {
    return view('pages.admin_review');
});

