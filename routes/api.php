<?php

use App\Http\Controllers\Api\v1\Color\ColorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\Compound\CompoundController;
use App\Http\Controllers\Api\v1\Brand\BrandController;
use App\Http\Controllers\Api\v1\Design\DesignController;
use App\Http\Controllers\Api\v1\Zone\ZoneController;
use App\Http\Controllers\Api\v1\Area\AreaController;
use App\Http\Controllers\Api\v1\Parking\ParkingTypeController;
use App\Http\Controllers\Api\v1\Parking\ParkingController;
use App\Http\Controllers\Api\v1\Block\BlockController;
use App\Http\Controllers\Api\v1\Parking\ParkingDesignController;
use App\Http\Controllers\Api\v1\Row\RowController;
use App\Http\Controllers\Api\v1\Slot\SlotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSend'])->name('password.send');
Route::post('/forgot-password-check', [AuthController::class, 'forgotPasswordCheckToken'])->name('password.check');
Route::post('/forgot-password-reset', [AuthController::class, 'forgotPasswordReset'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'auth'], function() {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function() {

    // Users
    Route::get('/me', [UserController::class, 'me']);
    Route::patch('/users/{id}', [UserController::class, 'restore'])->name('users.restore');
    Route::post('/users/generate-username', [UserController::class, 'generateUsername'])->name('users.generate-username');
    Route::resource('users', UserController::class, ['except' =>['create', 'edit']]);

    // Colors
    Route::patch('/colors/{id}', [ColorController::class, 'restore'])->name('colors.restore');
    Route::resource('colors', ColorController::class, ['except' =>['create', 'edit']]);

    // Compounds
    Route::patch('/compounds/{id}', [CompoundController::class, 'restore'])->name('compounds.restore');
    Route::resource('compounds', CompoundController::class, ['except' =>['create', 'edit']]);

    // Brands
    Route::patch('/brands/{id}', [BrandController::class, 'restore'])->name('brands.restore');
    Route::resource('brands', BrandController::class, ['except' =>['create', 'edit']]);

    // Designs
    Route::patch('/designs/{id}', [DesignController::class, 'restore'])->name('designs.restore');
    Route::resource('designs', DesignController::class, ['except' =>['create', 'edit']]);

    // Zones
    Route::patch('/zones/{id}', [ZoneController::class, 'restore'])->name('zones.restore');
    Route::resource('zones', ZoneController::class, ['except' =>['create', 'edit']]);

    // Areas
    Route::patch('/areas/{id}', [AreaController::class, 'restore'])->name('areas.restore');
    Route::resource('areas', AreaController::class, ['except' =>['create', 'edit', 'update']]);

    // Parking Types
    Route::patch('/parking-types/{id}', [ParkingTypeController::class, 'restore'])->name('parking-types.restore');
    Route::resource('parking-types', ParkingTypeController::class, ['except' =>['create', 'edit']]);

    // Parkings
    Route::patch('/parkings/{id}', [ParkingController::class, 'restore'])->name('parkings.restore');
    Route::post('/parking-design', [ParkingDesignController::class, 'parkingDesign'])->name('parkingDesign');
    Route::resource('parkings', ParkingController::class, ['except' =>['create', 'edit', 'store']]);

    // Blocks
    Route::patch('/blocks/{id}', [BlockController::class, 'restore'])->name('blocks.restore');
    Route::resource('blocks', BlockController::class, ['except' =>['create', 'edit']]);

    // Rows
    Route::resource('rows', RowController::class, ['except' =>['create', 'edit', 'store', 'delete']]);

    // Slots
    Route::resource('slots', SlotController::class, ['except' =>['create', 'edit', 'store', 'delete']]);
});
