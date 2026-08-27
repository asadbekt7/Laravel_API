<?php

use App\Http\Controllers\TypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\Api\InventoryImportController;
use App\Http\Controllers\Api\UzasboImportController;
use App\Http\Controllers\Api\ItemsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StaffNameController;
use App\Http\Controllers\RoomNameController;
use App\Http\Controllers\ContractAPI\InformationController;
use App\Http\Controllers\ContractAPI\InformationItemController;
use App\Http\Controllers\ContractAPI\ReceivingController;
use App\Http\Controllers\Api\WarehouseTransferController;
use App\Http\Controllers\Api\BugalteriyaController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\WarehouseBatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = $request->user();
    $claims = $user->ssoClaims();

    return [
        'id'            => $user->id,
        'staff_id'      => $user->staff_id,
        'full_name'     => $user->full_name,
        'email'         => $user->email,
        'permissions'   => $claims?->permissions ?? [],
    ];
});

//Type
Route::apiResource('types', TypeController::class)
    ->middleware('crud:ombor.types');

//Category
Route::apiResource('categories', CategoryController::class)
    ->middleware('crud:ombor.categories');

//Model
Route::apiResource('models', ModelController::class)
    ->middleware('crud:ombor.models');

//Unit
Route::apiResource('units', UnitController::class)
    ->middleware('crud:ombor.units');

//Location
Route::apiResource('locations', LocationController::class)
    ->middleware('crud:ombor.locations');

//Document-Type
Route::apiResource('document-types', DocumentTypeController::class)
    ->middleware('crud:ombor.document-types');

//Receiving
Route::apiResource('receivings', ReceivingController::class)
    ->middleware('crud:ombor.receivings');

//Excel import
Route::post('/inventory/import', InventoryImportController::class)
    ->middleware('perm:ombor.inventory.import')
    ->name('inventory.import');

//Uzasbo Import va Transfer
Route::prefix('uzasbo-imports')->group(function () {
    Route::get('/',           [UzasboImportController::class, 'index'])->middleware('perm:ombor.uzasbo.view');
    Route::get('/{id}',       [UzasboImportController::class, 'show'])->middleware('perm:ombor.uzasbo.view');
    Route::post('/transfer',  [UzasboImportController::class, 'transfer'])->middleware('perm:ombor.uzasbo.transfer');
});

//Items
Route::prefix('items')->middleware('crud:ombor.items')->group(function () {
    Route::get('/',      [ItemsController::class, 'index']);
    Route::get('/{id}',  [ItemsController::class, 'show']);
    Route::put('/{id}',   [ItemsController::class, 'update']);
    Route::patch('/{id}', [ItemsController::class, 'update']);
});

//Staff Search
Route::get('/staff/search', [StaffController::class, 'search']);
    // todo ->middleware('perm:ombor.staff.view');

//Supplier
Route::apiResource('suppliers', SupplierController::class)
    ->middleware('crud:ombor.suppliers');

//StaffName
Route::controller(StaffNameController::class)
    ->middleware('perm:ombor.staff-names.view')
    ->group(function () {
        Route::get('/staff-names',       'index');
        Route::get('/staff-names/items', 'show');
    });

//RoomName
Route::prefix('rooms')
    ->controller(RoomNameController::class)
    ->middleware('perm:ombor.rooms.view')
    ->group(function () {
        Route::get('/',                 'index');
        Route::get('/{roomName}',       'show');
        Route::get('/{roomName}/items', 'items');
    });

//Bugalteriya
Route::prefix('bugalteriya')->group(function () {
    Route::get('/',              [BugalteriyaController::class, 'index']); // todo ->middleware('perm:ombor.bugalteriya.view');
    Route::get('/{bugalteriya}', [BugalteriyaController::class, 'show']); // todo ->middleware('perm:ombor.bugalteriya.view');
    Route::post('/{bugalteriya}/complete', [BugalteriyaController::class, 'complete']); // todo ->middleware('perm:ombor.bugalteriya.complete');
    Route::post('/{bugalteriya}/cancel',   [BugalteriyaController::class, 'cancel']); // todo ->middleware('perm:ombor.bugalteriya.cancel');
});

//Information
Route::prefix('information')->group(function () {
    Route::get('pending', [InformationController::class, 'pending']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.view');

    Route::get('',  [InformationController::class, 'index']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.view');
    Route::post('', [InformationController::class, 'store']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.create');

    Route::get('{information}',    [InformationController::class, 'show']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.view');
    Route::put('{information}',    [InformationController::class, 'update']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
    Route::delete('{information}', [InformationController::class, 'destroy']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.delete');

    Route::post('{information}/accept',   [InformationController::class, 'accept']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
    Route::post('{information}/start',    [InformationController::class, 'start']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
    Route::post('{information}/complete', [InformationController::class, 'complete']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');

    Route::post('{information}/items',          [InformationItemController::class, 'store']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
    Route::put('{information}/items/{item}',    [InformationItemController::class, 'update']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
    Route::delete('{information}/items/{item}', [InformationItemController::class, 'destroy']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.informations.update');
});

//Warehouse
Route::prefix('warehouse')->group(function () {
    Route::get('/',           [WarehouseController::class, 'index'])->middleware('perm:ombor.warehouse.view');
    Route::get('/item-types', [WarehouseController::class, 'itemTypes'])->middleware('perm:ombor.warehouse.view');
    Route::get('/stats/categories', [WarehouseController::class, 'statsCategories']);
    Route::get('/stats/models',     [WarehouseController::class, 'statsModels']);

    Route::post('/',          [WarehouseController::class, 'store'])->middleware('perm:ombor.warehouse.create');
    Route::get('/{id}',       [WarehouseController::class, 'show'])->middleware('perm:ombor.warehouse.view');
});

//WarehouseTransfer todo ->middleware('perm:ombor.warehouse.update')
Route::prefix('warehouse-transfer')->group(function () {
    Route::get('staff-search', [WarehouseTransferController::class, 'staffSearch']);
    Route::post('',            [WarehouseTransferController::class, 'store']);
});

//Transfer
Route::prefix('transfers')->middleware('perm:ombor.transfers.create')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});

//WarehouseBatch
Route::get('warehouse-batches', [WarehouseBatchController::class, 'index']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.view');
Route::post('warehouse-batches', [WarehouseBatchController::class, 'store']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.create');
Route::get('warehouse-batches/{batch}', [WarehouseBatchController::class, 'show']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.view');
Route::get('warehouse-batches/{batch}/items', [WarehouseBatchController::class, 'items']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.view');
Route::post('warehouse-batches/{batch}/sign', [WarehouseBatchController::class, 'signAsAccountant']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.sign');
Route::post('warehouse-batches/{batch}/approve', [WarehouseBatchController::class, 'approve']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.approve');
Route::post('warehouse-batches/{batch}/reject', [WarehouseBatchController::class, 'reject']); //todo ishlar tugagach ushbu permissionlar ochib qo'yiladi; ->middleware('perm:ombor.warehouse-batches.approve');

Route::get('warehouse-batches/{batch}/pdf', [WarehouseBatchController::class, 'pdf'])
    ->middleware('throttle:20,1')
    ->name('warehouse-batches.pdf');

Route::prefix('receiving')
    ->name('receiving.')
    ->controller(ReceivingController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/{aktNumber}', 'show')
            ->where('aktNumber', '[A-Za-z0-9\-\.]+')
            ->name('show');

        Route::get('/{aktNumber}/pdf', 'pdf')
            ->where('aktNumber', '[A-Za-z0-9\-\.]+')
            ->middleware('throttle:20,1')
            ->name('pdf');
    });

