<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StampingController;
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
    // ユーザー関係
    Route::get('/admin', [UserController::class, 'index'])->name('admin.index');
    Route::get('/admin/user', [UserController::class, 'search'])->name('admin.user.search');
});

require __DIR__.'/auth.php';
