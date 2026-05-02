<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\StaffPortalController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'landing'])->name('landing');

Route::get('/login', function () {
    return redirect()->route('login.form', ['role' => 'student']);
})->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/{role}/login', [AuthController::class, 'showLogin'])
        ->whereIn('role', ['student', 'staff', 'admin'])
        ->name('login.form');

    Route::post('/{role}/login', [AuthController::class, 'login'])
        ->whereIn('role', ['student', 'staff', 'admin'])
        ->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AccountController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [AccountController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AccountController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [AccountController::class, 'editPassword'])->name('profile.password.edit');
        Route::put('/profile/password', [AccountController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/medical-leaves', [StudentPortalController::class, 'storeMedicalLeave'])->name('medical-leaves.store');
        Route::post('/electives', [StudentPortalController::class, 'storeElectiveSelection'])->name('electives.store');
        Route::post('/auxiliary-exams', [StudentPortalController::class, 'storeAuxiliaryExamRequest'])->name('auxiliary-exams.store');
        Route::post('/fees/{feePayment}/pay', [StudentPortalController::class, 'payFee'])->name('fees.pay');
        Route::get('/materials/{studyMaterial}/download', [StudentPortalController::class, 'downloadMaterial'])->name('materials.download');
    });

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AccountController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [AccountController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AccountController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [AccountController::class, 'editPassword'])->name('profile.password.edit');
        Route::put('/profile/password', [AccountController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/attendance', [StaffPortalController::class, 'storeAttendance'])->name('attendance.store');
        Route::post('/marks', [StaffPortalController::class, 'storeMarks'])->name('marks.store');
        Route::post('/materials', [StaffPortalController::class, 'storeMaterial'])->name('materials.store');
        Route::post('/alerts', [StaffPortalController::class, 'storeAlert'])->name('alerts.store');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/import', [AdminPortalController::class, 'importForm'])->name('import');
        Route::post('/import', [AdminPortalController::class, 'importStore'])->name('import.store');
        Route::get('/students', [AdminPortalController::class, 'studentsIndex'])->name('students.index');
        Route::get('/students/create', [AdminPortalController::class, 'studentsCreate'])->name('students.create');
        Route::post('/students', [AdminPortalController::class, 'studentsStore'])->name('students.store');
        Route::get('/students/{user}/edit', [AdminPortalController::class, 'studentsEdit'])->name('students.edit');
        Route::put('/students/{user}', [AdminPortalController::class, 'studentsUpdate'])->name('students.update');
        Route::delete('/students/{user}', [AdminPortalController::class, 'studentsDestroy'])->name('students.destroy');
        Route::get('/staff', [AdminPortalController::class, 'staffIndex'])->name('staff.index');
        Route::get('/staff/create', [AdminPortalController::class, 'staffCreate'])->name('staff.create');
        Route::post('/staff', [AdminPortalController::class, 'staffStore'])->name('staff.store');
        Route::get('/staff/{user}/edit', [AdminPortalController::class, 'staffEdit'])->name('staff.edit');
        Route::put('/staff/{user}', [AdminPortalController::class, 'staffUpdate'])->name('staff.update');
        Route::delete('/staff/{user}', [AdminPortalController::class, 'staffDestroy'])->name('staff.destroy');
    });