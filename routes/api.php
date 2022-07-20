<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\External\RecirculationController;
use App\Http\Controllers\Api\v1\Area\AreaController;
use App\Http\Controllers\Api\v1\Block\BlockController;
use App\Http\Controllers\Api\v1\Block\BlockRowController;
use App\Http\Controllers\Api\v1\Brand\BrandController;
use App\Http\Controllers\Api\v1\Carrier\CarrierController;
use App\Http\Controllers\Api\v1\Color\ColorController;
use App\Http\Controllers\Api\v1\Compound\CompoundController;
use App\Http\Controllers\Api\v1\Condition\ConditionController;
use App\Http\Controllers\Api\v1\Condition\ConditionModelDataController;
use App\Http\Controllers\Api\v1\Country\CountryController;
use App\Http\Controllers\Api\v1\Dealer\DealerController;
use App\Http\Controllers\Api\v1\Design\DesignController;
use App\Http\Controllers\Api\v1\Design\DesignSvgController;
use App\Http\Controllers\Api\v1\DestinationCode\DestinationCodeController;
use App\Http\Controllers\Api\v1\Device\DeviceController;
use App\Http\Controllers\Api\v1\DeviceType\DeviceTypeController;
use App\Http\Controllers\Api\v1\FreightVerify\VehicleReceivedController;
use App\Http\Controllers\Api\v1\Hold\HoldController;
use App\Http\Controllers\Api\v1\Load\LoadConfirmLeftController;
use App\Http\Controllers\Api\v1\Load\LoadController;
use App\Http\Controllers\Api\v1\Load\LoadGenerateController;
use App\Http\Controllers\Api\v1\Load\LoadTransportST8Controller;
use App\Http\Controllers\Api\v1\Load\LoadVehicleController;
use App\Http\Controllers\Api\v1\Movement\MovementController;
use App\Http\Controllers\Api\v1\Movement\MovementManualController;
use App\Http\Controllers\Api\v1\Movement\MovementRecommendController;
use App\Http\Controllers\Api\v1\Movement\MovementRectificationController;
use App\Http\Controllers\Api\v1\Notification\NotificationController;
use App\Http\Controllers\Api\v1\Page\PageController;
use App\Http\Controllers\Api\v1\Parking\ParkingController;
use App\Http\Controllers\Api\v1\Parking\ParkingDesignController;
use App\Http\Controllers\Api\v1\Parking\ParkingRowController;
use App\Http\Controllers\Api\v1\Parking\ParkingRowEspigaController;
use App\Http\Controllers\Api\v1\Parking\ParkingTypeController;
use App\Http\Controllers\Api\v1\Route\RouteController;
use App\Http\Controllers\Api\v1\RouteType\RouteTypeCarrierController;
use App\Http\Controllers\Api\v1\Row\RowRellocateController;
use App\Http\Controllers\Api\v1\Row\RowBlockController;
use App\Http\Controllers\Api\v1\Row\RowController;
use App\Http\Controllers\Api\v1\Row\RowVehicleController;
use App\Http\Controllers\Api\v1\Rule\RuleController;
use App\Http\Controllers\Api\v1\Slot\SlotController;
use App\Http\Controllers\Api\v1\State\StateController;
use App\Http\Controllers\Api\v1\State\StateVehicleController;
use App\Http\Controllers\Api\v1\Transport\TransportController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\Vehicle\StageController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleManualStoreController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleMovementsController;
use App\Http\Controllers\Api\v1\Vehicle\VehicleStageController;
use App\Http\Controllers\Api\v1\Zone\ZoneController;
use App\Http\Controllers\External\FORD\TransportST8Controller;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\v1\Recirculation\RecirculationController as RecirculationOwnerController;
use Illuminate\Support\Facades\Route;


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

Route::get('/environment', function () {
    return response()->json($_ENV);
});

Route::get('/dom-pdf', [TestController::class, 'domPdf']);
Route::get('/testing', [TestController::class, 'test']);

// Auth
Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSend'])->name('password.send');
Route::post('/forgot-password-check', [AuthController::class, 'forgotPasswordCheckToken'])->name('password.check');
Route::post('/forgot-password-reset', [AuthController::class, 'forgotPasswordReset'])->name('password.reset');

// API ST7 - Tracking Points
Route::post('/tracking-points', [VehicleStageController::class, 'vehicleStage'])->name('vehicleStage');

// Auth
Route::group(['prefix' => 'auth'], function() {
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

// API V1
Route::group(['prefix' => 'v1'], function() {

    // Devices Types
    Route::resource('devices-types', DeviceTypeController::class, ['only' => ['index']]);

    // Compounds
    Route::get('/compounds', [CompoundController::class, 'index'])->name('compounds.index');

    // Pages
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');

    // Devices
    Route::get('/devices/search-by-uuid/{uuid}', [DeviceController::class, 'searchByUuid'])->name('devices.search-by-uuid');
    Route::resource('devices', DeviceController::class, ['only' => ['store']]);

    // Designs
    Route::get('/designs/svg-default/{filename}', [DesignSvgController::class, 'default'])->name('designs-svg.default');

    Route::group(['middleware' => 'auth:sanctum'], function() {

        // Users
        Route::get('/me', [UserController::class, 'me']);
        Route::post('/users/datatables', [UserController::class, 'datatables'])->name('users.datatables');
        Route::patch('/users/{id}', [UserController::class, 'restore'])->name('users.restore');
        Route::post('/users/generate-username', [UserController::class, 'generateUsername'])->name('users.generate-username');
        Route::resource('users', UserController::class, ['except' =>['create', 'edit']]);

        // Colors
        Route::post('/colors/datatables', [ColorController::class, 'datatables'])->name('colors.datatables');
        Route::patch('/colors/{id}', [ColorController::class, 'restore'])->name('colors.restore');
        Route::resource('colors', ColorController::class, ['except' =>['create', 'edit']]);

        // Compounds
        Route::post('/compounds/datatables', [CompoundController::class, 'datatables'])->name('compounds.datatables');
        Route::patch('/compounds/{id}', [CompoundController::class, 'restore'])->name('compounds.restore');
        Route::resource('compounds', CompoundController::class, ['except' => ['create', 'edit', 'index']]);

        // Brands
        Route::patch('/brands/{id}', [BrandController::class, 'restore'])->name('brands.restore');
        Route::resource('brands', BrandController::class, ['except' =>['create', 'edit']]);

        // Designs
        Route::post('/designs/datatables', [DesignController::class, 'datatables'])->name('designs.datatables');
        Route::patch('/designs/{id}', [DesignController::class, 'restore'])->name('designs.restore');
        Route::resource('designs', DesignController::class, ['except' =>['create', 'edit']]);

        // Countries
        Route::post('/countries/datatables', [CountryController::class, 'datatables'])->name('countries.datatables');
        Route::patch('/countries/{id}', [CountryController::class, 'restore'])->name('countries.restore');
        Route::resource('countries', CountryController::class, ['except' =>['create', 'edit']]);

        // Routes
        Route::patch('/routes/{id}', [RouteController::class, 'restore'])->name('routes.restore');
        Route::resource('routes', RouteController::class, ['except' =>['create', 'edit']]);

        // Destination Codes
        Route::post('/destination-codes/datatables', [DestinationCodeController::class, 'datatables'])->name('destination-codes.datatables');
        Route::patch('/destination-codes/{id}', [DestinationCodeController::class, 'restore'])->name('destination-codes.restore');
        Route::resource('destination-codes', DestinationCodeController::class, ['except' =>['create', 'edit']]);

        // Conditions
        Route::patch('/conditions/{id}', [ConditionController::class, 'restore'])->name('conditions.restore');
        Route::get('/conditions/{condition}/model-data', [ConditionModelDataController::class, 'index'])->name('conditions-model-data.index');
        Route::resource('conditions', ConditionController::class, ['except' =>['create', 'edit']]);

        // States
        Route::patch('/states/{id}', [StateController::class, 'restore'])->name('states.restore');
        Route::get('/states/{state}/vehicles', [StateVehicleController::class, 'index'])->name('states-vehicles.index');
        Route::resource('states', StateController::class, ['except' =>['create', 'edit']]);

        // Holds
        Route::post('/holds/datatables', [HoldController::class, 'datatables'])->name('holds.datatables');
        Route::patch('/holds/{id}', [HoldController::class, 'restore'])->name('holds.restore');
        Route::patch('/holds/{hold}/toggle-active', [HoldController::class, 'toggleActive'])->name('holds.toggle-active');
        Route::resource('holds', HoldController::class, ['except' =>['create', 'edit']]);

        // Rules
        Route::post('/rules/datatables', [RuleController::class, 'datatables'])->name('rules.datatables');
        Route::patch('/rules/{id}', [RuleController::class, 'restore'])->name('rules.restore');
        Route::patch('/rules/{rule}/toggle-active', [RuleController::class, 'toggleActive'])->name('rules.toggle-active');
        Route::resource('rules', RuleController::class, ['except' =>['create', 'edit']]);

        // Zones
        Route::patch('/zones/{id}', [ZoneController::class, 'restore'])->name('zones.restore');
        Route::resource('zones', ZoneController::class, ['except' =>['create', 'edit']]);

        // Areas
        Route::post('/areas/datatables', [AreaController::class, 'datatables'])->name('areas.datatables');
        Route::patch('/areas/{id}', [AreaController::class, 'restore'])->name('areas.restore');
        Route::resource('areas', AreaController::class, ['except' =>['create', 'edit', 'update']]);

        // Parking Types
        Route::patch('/parking-types/{id}', [ParkingTypeController::class, 'restore'])->name('parking-types.restore');
        Route::resource('parking-types', ParkingTypeController::class, ['except' =>['create', 'edit']]);

        // Parkings
        Route::post('/parkings/datatables', [ParkingController::class, 'datatables'])->name('parkings.datatables');
        Route::patch('/parkings/{id}', [ParkingController::class, 'restore'])->name('parkings.restore');
        Route::patch('/parkings/{parking}/toggle-active', [ParkingController::class, 'toggleActive'])->name('parkings.toggle-active');
        Route::post('/parking-design', [ParkingDesignController::class, 'parkingDesign'])->name('parkingDesign');
        Route::resource('parkings', ParkingController::class, ['except' =>['create', 'edit', 'store']]);

        // Parkings Rows
        Route::get('/parkings/{parking}/rows', [ParkingRowController::class, 'index'])->name('parkings-rows.index');
        Route::get('/parkings/{parking}/row-espigas', [ParkingRowEspigaController::class, 'rowsSpikes'])->name('parking-rows.rows-spikes');

        // Blocks
        Route::patch('/blocks/{id}', [BlockController::class, 'restore'])->name('blocks.restore');
        Route::patch('/blocks/{block}/add-rows', [BlockController::class, 'addRows'])->name('blocks.add-rows');
        Route::patch('/blocks/{block}/toggle-active', [BlockController::class, 'toggleActive'])->name('blocks.toggle-active');
        Route::resource('blocks', BlockController::class, ['except' =>['create', 'edit']]);

        // Block Rows
        Route::get('/blocks/{block}/rows', [BlockRowController::class, 'index'])->name('blocks-rows.index');

        // Rows
        Route::put('/rows/{row}/rellocate', RowRellocateController::class)->name('rows.rellocate');
        Route::get('/rows/show-by-qrcode/{qrcode}', [RowController::class, 'showByQrCode'])->name('rows.show-by-qrcode');
        Route::patch('/rows/{row}/toggle-active', [RowController::class, 'toggleActive'])->name('rows.toggle-active');
        Route::get('/rows/{row}/vehicles', [RowVehicleController::class, 'index'])->name('rows-vehicles.index');
        Route::patch('/rows/{row}/blocks/unlink', [RowBlockController::class, 'unlink'])->name('rows-blocks.unlink');
        Route::patch('/rows/{row}/blocks/{block}', [RowBlockController::class, 'update'])->name('rows-blocks.update');
        Route::resource('rows', RowController::class, ['except' => ['create', 'edit', 'store', 'delete']]);

        // Slots
        Route::resource('slots', SlotController::class, ['except' =>['create', 'edit', 'store', 'delete']]);

        // Stages
        Route::patch('/stages/{id}', [StageController::class, 'restore'])->name('stages.restore');
        Route::resource('stages', StageController::class, ['except' =>['create', 'edit']]);

        // Vehicles
        Route::get('/vehicles/search-by-vin/{vin}', [VehicleController::class, 'searchByVin'])->name("vehicles.search-by-vin");
        Route::get('/vehicles/vin/{vin}', [VehicleMovementsController::class, 'vehicleMatchRules']);
        Route::patch('/vehicles/massive-change-data', [VehicleController::class, 'massiveChangeData'])->name('vehicles.massive-change-data');
        Route::patch('/vehicles/{id}', [VehicleController::class, 'restore'])->name('vehicles.restore');
        Route::patch('/vehicles/{vehicle}/change-position', [VehicleController::class, 'massiveChangeData'])->name('vehicles.change-position');
        Route::post('/vehicles/create-manual', VehicleManualStoreController::class)->name('vehicles.create-manual');
        Route::post('/vehicles/datatables', [VehicleController::class, 'datatables'])->name('vehicles.datatables');
        Route::resource('vehicles', VehicleController::class, ['except' =>['store', 'create', 'update', 'edit']]);

        // Transports
        Route::patch('/transports/{id}', [TransportController::class, 'restore'])->name('transports.restore');
        Route::patch('/transports/{transport}/toggle-active', [TransportController::class, 'toggleActive'])->name('transports.toggle-active');
        Route::resource('transports', TransportController::class, ['except' =>['create', 'edit']]);

        // Carriers
        Route::post('/carriers/datatables', [CarrierController::class, 'datatables'])->name('carriers.datatables');
        Route::patch('/carriers/{id}', [CarrierController::class, 'restore'])->name('carriers.restore');
        Route::resource('carriers', CarrierController::class, ['except' =>['create', 'edit']]);
        Route::post('/carriers/match-vins', [CarrierController::class, 'matchVins'])->name('carriers.match-vins');

        // Dealers
        Route::patch('/dealers/{id}', [DealerController::class, 'restore'])->name('dealers.restore');
        Route::resource('dealers', DealerController::class, ['except' =>['create', 'edit']]);

        // Notifications
        Route::post('/notifications/datatables', [NotificationController::class, 'datatables'])->name('notifications.datatables');
        Route::patch('/notifications/{id}', [NotificationController::class, 'restore'])->name('notifications.restore');
        Route::resource('notifications', NotificationController::class, ['except' => ['create', 'store', 'edit', 'update']]);

        // FreightVerify
        Route::post('/freight-verify/vehicle-received', VehicleReceivedController::class);

        // Loads
        Route::get('/loads', [LoadController::class, 'index'])->name('loads.index');
        Route::get('/loads/{load}/download-albaran', [LoadController::class, 'downloadAlbaran'])->name('loads.download-albaran');
        Route::post('/loads/datatables', [LoadController::class, 'datatables'])->name('loads.datatables');
        Route::post('/loads/{load}/vehicles/datatables', [LoadVehicleController::class, 'datatables'])->name('loads.vehicles.datatables');
        Route::patch('/loads/{load}/vehicles/{vehicle}/unlink', [LoadVehicleController::class, 'unlinkVehicle'])->name('loads.vehicles.unlink');
        Route::post('/loads/generate', [LoadGenerateController::class, 'generate'])->name('loads.generate');
        Route::patch('/loads/{load}/confirm-left', LoadConfirmLeftController::class)->name('loads.confirme-left');
        Route::post('/loads/check-vehicles', [LoadController::class, 'checkVehicles'])->name('loads.check-vehicles');

        // Movements
        Route::post('/movements/{movement}/rectification', [MovementRectificationController::class, 'update'])->name('movements.rectification');
        Route::post('/movements/reload', [MovementController::class, 'reload'])->name('movements.reload');
        Route::post('/movements/manual', [MovementManualController::class, 'manual'])->name('movements.manual');
        Route::post('/movements/manual/filtered-positions', [MovementManualController::class, 'filteredPositions'])->name('movements.filtered-positions');
        Route::put('/movements/{movement}/confirm', [MovementController::class, 'confirmMovement'])->name('movements.confirm-movement');
        Route::put('/movements/{movement}/cancel', [MovementController::class, 'cancelMovement'])->name('movements.cancel-movement');
        Route::post('/movements/recommend', [MovementRecommendController::class, 'index'])->name('movements.recommend');
        Route::post('/movements/datatables', [MovementController::class, 'datatables'])->name('movements.datatables');
        Route::resource('movements', MovementController::class, ['except' =>['create', 'edit', 'update', 'delete']]);

        // Valencia TSI Webservice ST8
        Route::get('/loads/{load}/transport-st8', LoadTransportST8Controller::class);

        Route::get('/routes-types/{routeType}/carriers', [RouteTypeCarrierController::class, "index"])->name("routes-types.carriers.index");

        // Recirculations
        Route::patch('/recirculations/{recirculation}/update-back', [RecirculationOwnerController::class, "updateBack"])->name("recirculations.update-back");
    });

});

// External
Route::group(['prefix' => 'external'], function() {
    Route::group(['middleware' => 'auth:sanctum'], function() {

        // Recirculations - SOAP FORD
        Route::get('/recirculations/{vin}', [RecirculationController::class, 'get'])->name('recirculations.get');
    });

    // API ST8
    Route::post('/transport-st8', TransportST8Controller::class);

});

Route::get('/send-row-notification', [TestController::class, 'sendRowNotification']);
