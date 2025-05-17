<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Employee\FinancialController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['check.role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
});

Route::prefix('employee/financial')->middleware('auth')->group(function () {
    Route::get('/', [FinancialController::class, 'index'])->name('employee.financial.index');
    Route::get('/create', [FinancialController::class, 'create'])->name('employee.financial.create');
    Route::post('/store', [FinancialController::class, 'store'])->name('employee.financial.store');
    Route::get('/{id}/edit', [FinancialController::class, 'edit'])->name('employee.financial.edit');
    Route::put('/{id}', [FinancialController::class, 'update'])->name('employee.financial.update');
    Route::delete('/{id}', [FinancialController::class, 'destroy'])->name('employee.financial.destroy');
});

Route::get('/', function () {
    return view('welcome');
})->name('welcome');