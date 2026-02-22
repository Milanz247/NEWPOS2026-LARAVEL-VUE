<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SystemCheckController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\BusinessLocationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Language Switch
Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');

// System Check Routes
Route::get('/check', [SystemCheckController::class, 'index'])->name('check.index');

// Registration Routes
Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');

// Admin Routes (Only for 'Owner' role)
Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':Owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    // Business Settings
    Route::get('settings', [BusinessSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');
    Route::put('settings/financials', [BusinessSettingController::class, 'updateFinancials'])->name('settings.financials');
    Route::put('settings/system', [BusinessSettingController::class, 'updateSystem'])->name('settings.system');
    Route::post('settings/logo', [BusinessSettingController::class, 'uploadLogo'])->name('settings.logo');
    Route::delete('settings/logo', [BusinessSettingController::class, 'deleteLogo'])->name('settings.logo.delete');

    // Business Locations
    Route::get('locations', [BusinessLocationController::class, 'index'])->name('locations.index');
    Route::post('locations', [BusinessLocationController::class, 'store'])->name('locations.store');
    Route::put('locations/{location}', [BusinessLocationController::class, 'update'])->name('locations.update');
    Route::delete('locations/{location}', [BusinessLocationController::class, 'destroy'])->name('locations.destroy');
});

require __DIR__.'/auth.php';
