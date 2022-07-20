<?php

use App\Http\Controllers\Api\v1\Load\LoadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test',[TestController::class,'test'])->name('test');

Route::get('/test/script-sql',[TestController::class,'script'])->name('test.script-sql');

// Route::get('/loads/{load}/download-albaran', [LoadController::class, 'downloadAlbaran'])->name('loads.download-albaran');
