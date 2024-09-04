<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StampingController;
use App\Http\Controllers\Admin\AdministratorController;
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
    Route::get('/admin', [UserController::class, 'index'])->name('admin.index');
    // 管理者関係
    Route::get('/admin/administrator/edit', [AdministratorController::class, 'editAdmin'])->name('admin.administrator.edit');
    Route::post('/admin/administrator/confirm', [AdministratorController::class, 'confirmAdmin'])->name('admin.administrator.confirm');
    Route::get('/admin/administrator/confirm', function () {
        return redirect('/');
    });
    Route::put('/admin/administrator/update', [AdministratorController::class, 'updateAdmin'])->name('admin.administrator.update');
    Route::get('/admin/administrator/update', function () {
        return redirect('/');
    });
    // ユーザー関係
    Route::get('/admin/user', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/admin/user/confirm', [UserController::class, 'confirmCreate'])->name('admin.user.confirmCreate');
    Route::get('/admin/user/confirm', function () {
        return redirect('/');
    });
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/admin/user/store', function () {
        return redirect('/');
    });
    Route::get('/admin/user/{user}', [UserController::class, 'edit'])
        ->where('user', '[0-9]+')
        ->name('admin.user.edit');
    Route::post('/admin/user/{user}/confirm', [UserController::class, 'confirmEdit'])
        ->where('user', '[0-9]+')
        ->name('admin.user.confirmEdit');
    Route::get('/admin/user/{user}/confirm', function () {
        return redirect('/');
    })->where('user', '[0-9]+');
    Route::put('/admin/user/{user}/update', [UserController::class, 'update'])
        ->where('user', '[0-9]+')
        ->name('admin.user.update');
    Route::get('/admin/user/{user}/update', function () {
        return redirect('/');
    })->where('user', '[0-9]+');
    Route::delete('/admin/user/{user}/destroy', [UserController::class, 'destroy'])
        ->where('user', '[0-9]+')
        ->name('admin.user.destroy');
    Route::get('/admin/user/{user}/destroy', function () {
        return redirect('/');
    })->where('user', '[0-9]+');
    // 勤怠関係
    Route::get('/admin/attendance/{user}', [AttendanceController::class, 'index'])
        ->where('user', '[0-9]+')
        ->name('admin.attendance.index');
});

require __DIR__.'/auth.php';
