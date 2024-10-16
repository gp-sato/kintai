<?php

use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CsvController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\StampingController;
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
    /**
     * 一般ユーザー
     */
    Route::group(['middleware' => 'user'], function () {
        // 勤怠管理打刻
        Route::get('/stamping', [StampingController::class, 'index'])->name('stamping.index');
        Route::post('/stamping', [StampingController::class, 'store'])->name('stamping.store');
    });
    /**
     * 管理者
     */
    Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        // 管理者関係
        Route::group(['prefix' => 'administrator', 'as' => 'administrator.'], function () {
            Route::get('/', [AdministratorController::class, 'editAdmin'])->name('edit');
            Route::post('/', [AdministratorController::class, 'confirmAdmin'])->name('confirm');
            Route::put('/', [AdministratorController::class, 'updateAdmin'])->name('update');
        });
        // ユーザー関係
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/', [UserController::class, 'create'])->name('create');
            Route::post('confirm', [UserController::class, 'confirmCreate'])->name('confirmCreate');
            Route::get('confirm', function () {
                return redirect('/');
            });
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('{user}', [UserController::class, 'edit'])
                ->where('user', '[0-9]+')
                ->name('edit');
            Route::post('{user}', [UserController::class, 'confirmEdit'])
                ->where('user', '[0-9]+')
                ->name('confirmEdit');
            Route::put('{user}', [UserController::class, 'update'])
                ->where('user', '[0-9]+')
                ->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])
                ->where('user', '[0-9]+')
                ->name('destroy');
        });
        // 勤怠関係
        Route::group(['prefix' => 'attendance', 'as' => 'attendance.'], function () {
            Route::get('/user/{user}', [AttendanceController::class, 'index'])
                ->where('user', '[0-9]+')
                ->name('index');
            Route::get('/user/{user}/create', [AttendanceController::class, 'create'])
                ->where('user', '[0-9]+')
                ->name('create');
            Route::post('/user/{user}', [AttendanceController::class, 'store'])
                ->where('user', '[0-9]+')
                ->name('store');
            Route::get('{attendance}', [AttendanceController::class, 'edit'])
                ->where('attendance', '[0-9]+')
                ->name('edit');
            Route::put('{attendance}', [AttendanceController::class, 'update'])
                ->where('attendance', '[0-9]+')
                ->name('update');
            Route::delete('{attendance}', [AttendanceController::class, 'destroy'])
                ->where('attendance', '[0-9]+')
                ->name('destroy');
        });
        // CSV関係
        Route::group(['prefix' => 'csv', 'as' => 'csv.'], function () {
            Route::get('/', [CsvController::class, 'index'])->name('index');
            Route::post('/', [CsvController::class, 'upload'])->name('upload');
            Route::get('/download', [CsvController::class, 'download'])->name('download');
        });
    });
});

require __DIR__.'/auth.php';
