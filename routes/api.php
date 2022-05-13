<?php

use App\Http\Controllers\Api\v1\Block\BlockRowController;
use App\Http\Controllers\Api\v1\Row\RowBlockController;
use App\Http\Controllers\Api\v1\Transport\TransportController;
use App\Http\Controllers\Api\v1\Carrier\CarrierController;
use App\Http\Controllers\Api\v1\Color\ColorController;
use App\Http\Controllers\Api\v1\Condition\ConditionModelDataController;
use App\Http\Controllers\Api\v1\Parking\ParkingRowController;
use App\Http\Controllers\Api\v1\Row\RowVehicleController;
use App\Http\Controllers\Api\v1\State\StateVehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\Compound\CompoundController;
use App\Http\Controllers\Api\v1\Brand\BrandController;
use App\Http\Controllers\Api\v1\Design\DesignController;
use App\Http\Controllers\Api\v1\Country\CountryController;
use App\Http\Controllers\Api\v1\Route\RouteController;
use App\Http\Controllers\Api\v1\DestinationCode\DestinationCodeController;
use App\Http\Controllers\Api\v1\Condition\ConditionController;
use App\Http\Controllers\Api\v1\State\StateController;
use App\Http\Controllers\Api\v1\Hold\HoldController;
use App\Http\Controllers\Api\v1\Rule\RuleController;
use App\Http\Controllers\Api\v1\Zone\ZoneController;
use App\Http\Controllers\Api\v1\Area\AreaController;
use App\Http\Controllers\Api\v1\Parking\ParkingTypeController;
use App\Http\Controllers\Api\v1\Parking\ParkingController;
use App\Http\Controllers\Api\v1\Parking\ParkingDesignController;
use App\Http\Controllers\Api\v1\Block\BlockController;
use App\Http\Controllers\Api\v1\Row\RowController;
use App\Http\Controllers\Api\v1\Slot\SlotController;
use App\Http\Controllers\Api\v1\Vehicle\StageController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleStageController;

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

Route::post('/tracking-points', [VehicleStageController::class, 'vehicleStage'])->name('vehicleStage');

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
    Route::post('/colors/datatables', [ColorController::class, 'datatables'])->name('colors.datatables');
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

    // Countries
    Route::patch('/countries/{id}', [CountryController::class, 'restore'])->name('countries.restore');
    Route::resource('countries', CountryController::class, ['except' =>['create', 'edit']]);

    // Routes
    Route::patch('/routes/{id}', [RouteController::class, 'restore'])->name('routes.restore');
    Route::resource('routes', RouteController::class, ['except' =>['create', 'edit']]);

    // Destination Codes
    Route::patch('/destination-codes/{id}', [DestinationCodeController::class, 'restore'])->name('destination-codes.restore');
    Route::resource('destination-codes', DestinationCodeController::class, ['except' =>['create', 'edit']]);

    // Conditions
    Route::patch('/conditions/{id}', [ConditionController::class, 'restore'])->name('conditions.restore');
    Route::get('/conditions/{condition}/model-data', [ConditionModelDataController::class, 'index'])->name('conditions-model-data.index');
    Route::resource('conditions', ConditionController::class, ['except' =>['create', 'edit']]);

    // States
    Route::patch('/states/{id}', [StateController::class, 'restore'])->name('states.restore');
    Route::resource('states', StateController::class, ['except' =>['create', 'edit']]);
    Route::get('/states/{state}/vehicles', [StateVehicleController::class, 'index'])->name('states-vehicles.index');

    // Holds
    Route::patch('/holds/{id}', [HoldController::class, 'restore'])->name('holds.restore');
    Route::patch('/holds/{hold}/toggle-active', [HoldController::class, 'toggleActive'])->name('holds.toggle-active');
    Route::resource('holds', HoldController::class, ['except' =>['create', 'edit']]);

    // Rules
    Route::patch('/rules/{id}', [RuleController::class, 'restore'])->name('rules.restore');
    Route::patch('/rules/{rule}/toggle-active', [RuleController::class, 'toggleActive'])->name('rules.toggle-active');
    Route::resource('rules', RuleController::class, ['except' =>['create', 'edit']]);

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
    Route::patch('/parkings/{parking}/toggle-active', [ParkingController::class, 'toggleActive'])->name('parkings.toggle-active');
    Route::post('/parking-design', [ParkingDesignController::class, 'parkingDesign'])->name('parkingDesign');
    Route::resource('parkings', ParkingController::class, ['except' =>['create', 'edit', 'store']]);

    // Parkings Rows
    Route::get('/parkings/{parking}/rows', [ParkingRowController::class, 'index'])->name('parkings-rows.index');

    // Blocks
    Route::patch('/blocks/{id}', [BlockController::class, 'restore'])->name('blocks.restore');
    Route::patch('/blocks/{block}/add-rows', [BlockController::class, 'addRows'])->name('blocks.add-rows');
    Route::patch('/blocks/{block}/toggle-active', [BlockController::class, 'toggleActive'])->name('blocks.toggle-active');
    Route::resource('blocks', BlockController::class, ['except' =>['create', 'edit']]);

    // Block Rows
    Route::get('/blocks/{block}/rows', [BlockRowController::class, 'index'])->name('blocks-rows.index');

    // Rows
    Route::patch('/rows/{row}/toggle-active', [RowController::class, 'toggleActive'])->name('rows.toggle-active');
    Route::resource('rows', RowController::class, ['except' =>['create', 'edit', 'store', 'delete']]);
    Route::get('/rows/{row}/vehicles', [RowVehicleController::class, 'index'])->name('rows-vehicles.index');
    Route::patch('/rows/{row}/blocks/unlink', [RowBlockController::class, 'unlink'])->name('rows-blocks.unlink');
    Route::patch('/rows/{row}/blocks/{block}', [RowBlockController::class, 'update'])->name('rows-blocks.update');

    // Slots
    Route::resource('slots', SlotController::class, ['except' =>['create', 'edit', 'store', 'delete']]);

    // Stages
    Route::patch('/stages/{id}', [StageController::class, 'restore'])->name('stages.restore');
    Route::resource('stages', StageController::class, ['except' =>['create', 'edit']]);

    // Vehicles
    Route::patch('/vehicles/{id}', [VehicleController::class, 'restore'])->name('vehicles.restore');
    Route::get('/vehicles/{vehicle}/detail', [VehicleController::class, 'detail'])->name('vehicles.detail');
    Route::post('/vehicles/datatables', [VehicleController::class, 'datatables'])->name('vehicles.datatables');
    Route::resource('vehicles', VehicleController::class, ['except' =>['store', 'create', 'update', 'edit']]);

    // Transports
    Route::patch('/transports/{id}', [TransportController::class, 'restore'])->name('transports.restore');
    Route::patch('/transports/{transport}/toggle-active', [TransportController::class, 'toggleActive'])->name('transports.toggle-active');
    Route::resource('transports', TransportController::class, ['except' =>['create', 'edit']]);

    // Carriers
    Route::patch('/carriers/{id}', [CarrierController::class, 'restore'])->name('carriers.restore');
    Route::resource('carriers', CarrierController::class, ['except' =>['create', 'edit']]);
});
