<?php

use Illuminate\Support\Facades\Route;
// Auth
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserControllerUser;

// User
use App\Http\Controllers\User\ProfileController;

Route::middleware(['check.role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    // Quản lý user
    Route::prefix('admin')->group(function () {
        Route::get('/users', [UserControllerUser::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [UserControllerUser::class, 'show'])->name('admin.users.show');
        Route::get('/users/{id}/edit', [UserControllerUser::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{id}', [UserControllerUser::class, 'update'])->name('admin.users.update');

        Route::post('/users/{id}/add-balance', [UserControllerUser::class, 'addBalance'])->name('admin.users.addBalance');
        Route::get('/users/{id}/toggle-status', [UserControllerUser::class, 'toggleStatus'])->name('admin.users.toggleStatus');
    });
});

Route::middleware(['check.role:superadmin'])->group(function () {
    Route::prefix('superadmin')->group(function () {
        Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
    });
});

Route::prefix('user')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

// Routes đăng ký cho người dùng thông thường
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Routes đăng nhập cho người dùng thông thường
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'loginUser'])->name('user.login');

// Routes đăng nhập cho quản trị viên
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->name('admin.login');

// Route đăng xuất
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route trang chủ
Route::get('/', function () {
    return view('index');
})->name('home');

// Route 404
Route::get('/404', function () {
    return view('errors.404');
})->name('404');
