<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});
Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::get('/admin', function () {
    return redirect()->to('/admin/login');
});
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});
Route::get('/requestbrochure', function () {
    return view('request.form');
});
Route::get('/requestbrochure-list', function () {
    return view('request.list');
});
Route::get('/requestbrochure-logistics', function () {
    return redirect()->to(url('admin/dashboard') . '?section=logistics');
});
Route::get('/requestbrochure-completed', function () {
    return view('request.completed');
});
