<?php

use App\Http\Controllers\TypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\Api\ReceivingController;
use App\Http\Controllers\Api\InventoryImportController;
use App\Http\Controllers\Api\UzasboImportController;
use App\Http\Controllers\Api\ItemsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StaffNameController;
use App\Http\Controllers\RoomNameController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\Api\YetkazuvchiController;
use App\Http\Controllers\Api\MalumotController;


use App\Http\Controllers\Api\WarehouseController;




use App\Http\Controllers\Api\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Type
Route::apiResource('types', TypeController::class);

//Category
Route::apiResource('categories', CategoryController::class);

//Model
Route::apiResource('models', ModelController::class);

//Unit
Route::apiResource('units', UnitController::class);

//location
Route::apiResource('locations', LocationController::class);

//Document-Type
Route::apiResource('document-types', DocumentTypeController::class);

//Receiving
Route::apiResource('receivings', ReceivingController::class);

//Excel import
Route::post('/inventory/import', InventoryImportController::class)
    ->name('inventory.import');

//Uzasbo Import va Transfer
Route::prefix('uzasbo-imports')->group(function () {
    Route::get('/',           [UzasboImportController::class, 'index']);
    Route::get('/{id}',       [UzasboImportController::class, 'show']);
    Route::post('/transfer',  [UzasboImportController::class, 'transfer']);
});

//Uzasbo-Import transfer GET
Route::prefix('items')->group(function () {
    Route::get('/',    [ItemsController::class, 'index']);
    Route::get('/{id}', [ItemsController::class, 'show']);
});
//Staff Search
Route::get('/staff/search', [StaffController::class, 'search']);

//Supplier
Route::apiResource('suppliers', SupplierController::class);

//StaffName
Route::controller(StaffNameController::class)->group(function () {
    Route::get('/staff-names',       'index');
    Route::get('/staff-names/items', 'show');
});

//RoomName
Route::prefix('rooms')->controller(RoomNameController::class)->group(function () {
    Route::get('/',                'index');   // ?search=... bilan filter
    Route::get('/{roomName}',      'show');    // API + DB birlashgan
    Route::get('/{roomName}/items','items');   // faqat DB statistika
});

//Information
Route::prefix('informations')->controller(InformationController::class)->group(function () {

    Route::get('/',                  'index');
    Route::post('/',                 'store');
    Route::get('/{id}',              'show');
    Route::put('/{id}',              'update');
    Route::delete('/{id}',           'destroy');
    Route::delete('/{id}/force',     'forceDestroy');
    Route::post('/{id}/restore',     'restore');

});

//Yetkazuvchi
Route::apiResource('yetkazuvchi', YetkazuvchiController::class);

//Malumot
Route::apiResource('malumots', MalumotController::class);




//Warehouse
Route::post('warehouse/bulk', [WarehouseController::class, 'bulkStore']);
Route::apiResource('warehouse', WarehouseController::class);

//Transfer
Route::prefix('transfers')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});
