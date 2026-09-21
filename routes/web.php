<?php

use Illuminate\Support\Facades\Route;



Route::redirect('/', '/login');
Route::view('/login', 'login')->name('login');

Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
Route::view('/admin/users', 'admin.users')->name('admin.users');
Route::view('/staff-secretary/dashboard', 'staff_secretary.dashboard')->name('staff_secretary.dashboard');
Route::view('/staff/dashboard', 'staff.dashboard')->name('staff.dashboard');
Route::view('/class-secretary/dashboard', 'class_secretary.dashboard')->name('class_secretary.dashboard');
