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
    Route::resource('platforms', PlatformController::class)->except(['show']);
    Route::resource('expense-types', ExpenseTypesController::class)->except(['show']);
});

Route::prefix('employee/financial')->middleware('auth')->group(function () {
    Route::get('/', [FinancialController::class, 'index'])->name('employee.financial.index');
    Route::get('/create', [FinancialController::class, 'create'])->name('employee.financial.create');
    Route::post('/store', [FinancialController::class, 'store'])->name('employee.financial.store');
    Route::get('/{id}/edit', [FinancialController::class, 'edit'])->name('employee.financial.edit');
    Route::put('/{id}', [FinancialController::class, 'update'])->name('employee.financial.update');
    Route::delete('/{id}', [FinancialController::class, 'destroy'])->name('employee.financial.destroy');
});


Route::prefix('manager/financial')->middleware(['auth'])->group(function () {
    Route::get('/financial', [FinancialApprovalController::class, 'index'])->name('manager.financial.index');
    Route::get('/financial/{id}', [FinancialApprovalController::class, 'show'])->name('manager.financial.show');
    Route::post('/financial/{id}/approve', [FinancialApprovalController::class, 'approve'])->name('manager.financial.approve');
    Route::post('/financial/{id}/reject', [FinancialApprovalController::class, 'reject'])->name('manager.financial.reject');
    
});

Route::prefix('admin/financial')->middleware(['auth'])->group(function () {
    Route::get('/financial', [FinancialAdminController::class, 'index'])->name('admin.financial.index');
    Route::post('/financial/approve/{id}', [FinancialAdminController::class, 'approve'])->name('admin.financial.approve');
    Route::get('/admin/financial/history', [FinancialAdminController::class, 'history'])->name('admin.financial.history');

});
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/admin/financial/total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('admin.financial.total_revenue');
Route::get('/admin/financial/revenue-data', [FinancialAdminController::class, 'revenueChartData'])->name('admin.financial.revenue_data');
Route::get('admin/financial/total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('admin.financial.total_revenue');
Route::get('admin/financial/filter-revenue', [FinancialAdminController::class, 'filterByDate'])->name('admin.financial.filter_by_date');


// Sai (gây lỗi):
Route::get('/admin/financial/filter', [FinancialAdminController::class, 'filterCustom']);

// Đúng (phù hợp với method trong controller):
Route::get('/admin/financial/history', [FinancialAdminController::class, 'history'])->name('admin.financial.history');
Route::get('/admin/financial/total-revenue', [FinancialAdminController::class, 'history'])->name('admin.financial.totalRevenue');

Route::get('/admin/financial/total-revenue', [FinancialAdminController::class, 'history'])
    ->name('admin.financial.total_revenue');

Route::prefix('admin/financial')->name('admin.financial.')->group(function () {
    // route hiển thị biểu đồ tổng doanh thu và lọc theo ngày
    Route::get('total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('total_revenue');
});
