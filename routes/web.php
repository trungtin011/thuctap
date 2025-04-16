<?php

use Illuminate\Support\Facades\Route;
// Auth
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Admin
use App\Http\Controllers\Admin\DashboardController;

// User


Route::get('/', function () {
    return view('index');
})->name('home');

Route::middleware(['check.role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);


// Routes cho người dùng thông thường
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'loginUser'])->name('user.login');
Route::get('/home', function () {
    return view('index');
})->middleware('check.role:user')->name('home');

// Routes cho quản trị viên
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->name('admin.login');
Route::get('/admin/dashboard', function () {
    return view('admin.index');
})->middleware('check.role:admin')->name('admin.dashboard');
Route::get('/superadmin/dashboard', function () {
    return view('superadmin.index');
})->middleware('check.role:superadmin')->name('superadmin.dashboard');

// Route đăng xuất
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route 404
Route::get('/404', function () {
    return view('errors.404');
})->name('404');
