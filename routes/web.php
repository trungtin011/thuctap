<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Employee\FinancialController;
use App\Http\Controllers\ExpenseTypesController;
use App\Http\Controllers\Manager\FinancialApprovalController;
use App\Http\Controllers\Admin\FinancialAdminController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['check.role:admin,manager'])->group(function () {
    
    // Đúng (phù hợp với method trong controller):
    Route::prefix('admin/financial')->name('admin.financial.')->group(function () {
        // Route hiển thị danh sách bản ghi tài chính
        Route::get('/', [FinancialAdminController::class, 'index'])->name('index');

        // Route phê duyệt bản ghi tài chính
        Route::post('/approve/{id}', [FinancialAdminController::class, 'approve'])->name('approve');

        // Route hiển thị lịch sử bản ghi đã phê duyệt
        Route::get('/history', [FinancialAdminController::class, 'history'])->name('history');

        // Route hiển thị tổng doanh thu và biểu đồ
        Route::get('/total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('total_revenue');
    });

    Route::prefix('manager/financial')->group(function () {
        Route::get('/financial', [FinancialApprovalController::class, 'index'])->name('manager.financial.index');
        Route::get('/financial/{id}', [FinancialApprovalController::class, 'show'])->name('manager.financial.show');
        Route::post('/financial/{id}/approve', [FinancialApprovalController::class, 'approve'])->name('manager.financial.approve');
        Route::post('/financial/{id}/reject', [FinancialApprovalController::class, 'reject'])->name('manager.financial.reject');
    });
});

Route::middleware(['check.role:admin'])->group(function () {
    Route::get('dashboard', [FinancialAdminController::class, 'totalRevenue'])->name('dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('platforms', PlatformController::class)->except(['show']);
    Route::resource('expense-types', ExpenseTypesController::class)->except(['show']);

    // Đúng (phù hợp với method trong controller):
    Route::prefix('admin/financial')->name('admin.financial.')->group(function () {
        // Route hiển thị danh sách bản ghi tài chính
        Route::get('/', [FinancialAdminController::class, 'index'])->name('index');

        // Route phê duyệt bản ghi tài chính
        Route::post('/approve/{id}', [FinancialAdminController::class, 'approve'])->name('approve');

        // Route hiển thị lịch sử bản ghi đã phê duyệt
        Route::get('/history', [FinancialAdminController::class, 'history'])->name('history');

        // Route hiển thị tổng doanh thu và biểu đồ
        Route::get('/total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('total_revenue');
    });
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


// Sai (gây lỗi):
Route::get('/admin/financial/filter', [FinancialAdminController::class, 'filterCustom']);
