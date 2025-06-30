<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Employee\FinancialController;
use App\Http\Controllers\ExpenseTypesController;
use App\Http\Controllers\Manager\FinancialApprovalController;
use App\Http\Controllers\Admin\FinancialAdminController;
use App\Http\Controllers\Admin\FinancialTargetController;
use App\Http\Controllers\Admin\DaiLyController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OfficeRevenueController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Manager\ExpenseApprovalController;
use App\Http\Controllers\Manager\OfficeRevenueController as ManagerOfficeRevenueController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['check.role:admin,manager'])->group(function () {
    Route::get('dashboard', [FinancialAdminController::class, 'totalRevenue'])->name('dashboard');

    // Route trả view cho biểu đồ
    Route::get('/revenue-chart', function () {
        return view('admin.financial.revenue-chart');
    })->name('revenue.chart');

    // Route API để lấy dữ liệu JSON
    Route::get('/api/revenue', [RevenueController::class, 'getRevenueData']);

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

        Route::get('/expenses', [ExpenseApprovalController::class, 'index'])->name('manager.expenses.index');

        Route::get('/expenses/{id}', [ExpenseApprovalController::class, 'show'])->name('manager.expenses.show');

        Route::post('/expenses/{id}/approve', [ExpenseApprovalController::class, 'approve'])->name('manager.expenses.approve');

        Route::post('/expenses/{id}/reject', [ExpenseApprovalController::class, 'reject'])->name('manager.expenses.reject');

        Route::middleware(['auth'])->group(function () {
            Route::get('/manager/office-revenues', [ManagerOfficeRevenueController::class, 'index'])->name('manager.office_revenues.index');
            Route::get('/manager/office-revenues/{id}', [ManagerOfficeRevenueController::class, 'show'])->name('manager.office_revenues.show');
            Route::post('/manager/office-revenues/{id}/approve', [ManagerOfficeRevenueController::class, 'approve'])->name('manager.office_revenues.approve');
            Route::post('/manager/office-revenues/{id}/reject', [ManagerOfficeRevenueController::class, 'reject'])->name('manager.office_revenues.reject');
            
        });
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

    // Mục tiêu doanh thu (admin)
    Route::prefix('admin/targets')->name('admin.targets.')->group(function () {
        Route::get('/create', [FinancialTargetController::class, 'create'])->name('create');
        Route::post('/store', [FinancialTargetController::class, 'store'])->name('store');
        Route::delete('/{id}', [FinancialTargetController::class, 'destroy'])->name('destroy');
    });

    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
    // Thêm route quản lý chuyến đi và hành khách
    Route::resource('trips', \App\Http\Controllers\TripController::class)->except(['show']);
    Route::resource('platforms', PlatformController::class)->except(['show']);
    Route::resource('expense-types', ExpenseTypesController::class)->except(['show']);

    Route::prefix('admin')->name('admin.')->group(function () {
        // Static route for index placed FIRST
        Route::get('/dai-ly', [DaiLyController::class, 'index'])->name('dai_ly.index');
        Route::get('/dai-ly/create', [DaiLyController::class, 'create'])->name('dai_ly.create');
        Route::post('/dai-ly', [DaiLyController::class, 'store'])->name('dai_ly.store');
        // Dynamic routes placed AFTER static routes
        Route::get('/dai-ly/{daiLy}/edit', [DaiLyController::class, 'edit'])->name('dai_ly.edit');
        Route::put('/dai-ly/{daiLy}', [DaiLyController::class, 'update'])->name('dai_ly.update');
        Route::delete('/dai-ly/{daiLy}', [DaiLyController::class, 'destroy'])->name('dai_ly.destroy');

        Route::get('/fields', [FieldController::class, 'index'])->name('truong.fields.index');
        Route::get('/fields/create', [FieldController::class, 'create'])->name('fields.create');
        Route::post('/fields', [FieldController::class, 'store'])->name('truong.fields.store');
        Route::get('/fields/{id}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('/fields/{id}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{id}', [FieldController::class, 'destroy'])->name('fields.destroy');
    });

    // Platform
    Route::prefix('platforms')->group(function () {
        Route::get('/', [PlatformController::class, 'index'])->name('platforms.index');
        Route::get('/create', [PlatformController::class, 'create'])->name('platforms.create');
        Route::post('/', [PlatformController::class, 'store'])->name('platforms.store');
        Route::get('/{platform}/edit', [PlatformController::class, 'edit'])->name('platforms.edit');
        Route::put('/{platform}', [PlatformController::class, 'update'])->name('platforms.update');
        Route::delete('/{platform}', [PlatformController::class, 'destroy'])->name('platforms.destroy');
        Route::delete('/{platform}/metrics/{metric}', [PlatformController::class, 'destroyMetric'])->name('platforms.metrics.destroy');
    });

    // Đúng (phù hợp với method trong controller):
    Route::prefix('admin/financial')->name('admin.financial.')->group(function () {
        // Route hiển thị danh sách bản ghi tài chính
        Route::get('/', [FinancialAdminController::class, 'index'])->name('index');

        // Route phê duyệt bản ghi tài chính
        Route::post('/approve/{id}', [FinancialAdminController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [FinancialAdminController::class, 'reject'])->name('reject');
        // Route hiển thị lịch sử bản ghi đã phê duyệt
        Route::get('/history', [FinancialAdminController::class, 'history'])->name('history');

        Route::post('/admin/financial/approve/{id}', [FinancialAdminController::class, 'approve'])->name('admin.financial.approve');
        Route::post('/admin/financial/reject/{id}', [FinancialAdminController::class, 'reject'])->name('admin.financial.reject');



        // Route hiển thị tổng doanh thu và biểu đồ
        Route::get('/total-revenue', [FinancialAdminController::class, 'totalRevenue'])->name('total_revenue');
    });

    // Thêm route cho mục tiêu doanh thu năm (admin)
    Route::post('/admin/financial/set-goal', [FinancialAdminController::class, 'setGoal'])->name('admin.financial.set_goal');

    // Nhập liệu tự động từ Excel
    Route::get('import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('import', [ImportController::class, 'import'])->name('import.excel');

    // Báo cáo doanh thu chi tiết
    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('reports/office', [ReportController::class, 'office'])->name('reports.office');
    Route::get('reports/trips', [ReportController::class, 'trips'])->name('reports.trips');
    Route::get('reports/commission', [ReportController::class, 'commission'])->name('reports.commission');

    // Quản lý phòng hàng & tuyến đường
    Route::resource('office-revenues', OfficeRevenueController::class)->except(['show']);
    Route::resource('trips', TripController::class)->except(['show']);
    // Thêm quản lý văn phòng (office)
    Route::resource('offices', OfficeController::class)->except(['show']);
});

Route::middleware(['auth'])->prefix('employee/financial')->name('employee.financial.')->group(function () {
    // Shared index for all departments
    Route::get('/', [FinancialController::class, 'index'])->name('index');

    // Marketing routes
    Route::get('/marketing/create', [FinancialController::class, 'createMarketing'])->name('create.marketing');
    Route::post('/marketing/store', [FinancialController::class, 'storeMarketing'])->name('store.marketing');
    Route::get('/marketing/{id}/edit', [FinancialController::class, 'editMarketing'])->name('edit.marketing');
    Route::put('/marketing/{id}', [FinancialController::class, 'updateMarketing'])->name('update.marketing');

    // Accounting routes
    Route::get('/accounting/create', [FinancialController::class, 'createAccounting'])->name('create.accounting');
    Route::post('/accounting/store', [FinancialController::class, 'storeAccounting'])->name('store.accounting');
    Route::get('/accounting/{id}/edit', [FinancialController::class, 'editAccounting'])->name('edit.accounting');
    Route::put('/accounting/{id}', [FinancialController::class, 'updateAccounting'])->name('update.accounting');

    // Business routes
    Route::get('/business/create', [FinancialController::class, 'createBusiness'])->name('create.business');
    Route::post('/business/store', [FinancialController::class, 'storeBusiness'])->name('store.business');
    Route::get('/business/{id}/edit', [FinancialController::class, 'editBusiness'])->name('edit.business');
    Route::put('/business/{id}', [FinancialController::class, 'updateBusiness'])->name('update.business');

    // Shared routes
    Route::delete('/{id}', [FinancialController::class, 'destroy'])->name('destroy');
    Route::get('/get-metrics/{platformId}', [FinancialController::class, 'getMetrics'])->name('get_metrics');
    Route::get('/get-metric-values/{metricId}', [FinancialController::class, 'getMetricValues'])->name('get_metric_values');
    Route::get('/get-record-metric-values/{recordId}', [FinancialController::class, 'getMetricValuesForRecord'])->name('get_record_metric_values');
});


Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// Sai (gây lỗi):
Route::get('/admin/financial/filter', [FinancialAdminController::class, 'filterCustom']);

Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.role:admin'])->group(function () {
    // Financial routes
    Route::get('/financial', [App\Http\Controllers\Admin\FinancialAdminController::class, 'index'])->name('financial.index');
    Route::get('/financial/total_revenue', [App\Http\Controllers\Admin\FinancialAdminController::class, 'totalRevenue'])->name('financial.total_revenue');
    Route::get('/financial/history', [App\Http\Controllers\Admin\FinancialAdminController::class, 'history'])->name('financial.history');
    Route::post('/financial/set-goal', [App\Http\Controllers\Admin\FinancialAdminController::class, 'setGoal'])->name('financial.set_goal');
    Route::post('/financial/{id}/approve', [App\Http\Controllers\Admin\FinancialAdminController::class, 'approve'])->name('financial.approve');
    Route::post('/financial/{id}/reject', [App\Http\Controllers\Admin\FinancialAdminController::class, 'reject'])->name('financial.reject');
});
