<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StampingController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('top');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // 勤怠管理打刻
    Route::get('/stamping', [StampingController::class, 'index'])->name('stamping.index');
    Route::post('/stamping', [StampingController::class, 'store'])->name('stamping.store');
    /**
     * 管理者
     */
    // 管理者関係
    Route::get('/admin', [UserController::class, 'index'])->name('admin.index');
    // ユーザー関係
    Route::get('/admin/user', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/admin/user/confirm', [UserController::class, 'confirmCreate'])->name('admin.user.confirm');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('admin.user.store');
    // 勤怠関係
    Route::get('/admin/attendance/{user}', [AttendanceController::class, 'index'])
        ->where('user', '[0-9]+')
        ->name('admin.attendance.index');
});

require __DIR__.'/auth.php';
