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
use App\Http\Controllers\ContractAPI\InformationController;
use App\Http\Controllers\Api\WarehouseTransferController;
use App\Http\Controllers\BugalteriyaController;

use App\Http\Controllers\Api\WarehouseController;




use App\Http\Controllers\Api\TransferController;
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
    Route::get('/',           [UzasboImportController::class, 'index'])->middleware('perm:ombor.uzasbo.view');
    Route::get('/{id}',       [UzasboImportController::class, 'show'])->middleware('perm:ombor.uzasbo.view');
    Route::post('/transfer',  [UzasboImportController::class, 'transfer'])->middleware('perm:ombor.uzasbo.transfer');
});

//Uzasbo-Import transfer GET
Route::prefix('items')->middleware('crud:ombor.items')->group(function () {
    Route::get('/',            [ItemsController::class, 'index']);
    Route::get('/{id}',        [ItemsController::class, 'show']);
    Route::put('items/{id}',   [ItemsController::class, 'update']);
    Route::patch('items/{id}', [ItemsController::class, 'update']);
});
//Staff Search
Route::get('/staff/search', [StaffController::class, 'search']);

//Supplier
Route::apiResource('suppliers', SupplierController::class)->middleware('crud:ombor.suppliers');

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

//Bugalteriya
Route::prefix('bugalteriya')->group(function () {
    Route::get('/', [BugalteriyaController::class, 'index']);              // ro'yxat (?status=pending)
    Route::get('/{bugalteriya}', [BugalteriyaController::class, 'show']);  // bitta yozuv
    Route::post('/{bugalteriya}/complete', [BugalteriyaController::class, 'complete']); // yakunlash -> items
    Route::post('/{bugalteriya}/cancel', [BugalteriyaController::class, 'cancel']);     // bekor qilish
});

Route::prefix('information')->group(function () {
    // Statik route'lar {information} parametridan OLDIN turishi shart
    Route::get('pending',  [InformationController::class, 'pending'])->middleware('perm:ombor.informations.view');
    Route::get('my-tasks', [InformationController::class, 'myTasks'])->middleware('perm:ombor.informations.view');
    Route::post('bulk',    [InformationController::class, 'bulkStore'])->middleware('perm:ombor.informations.create');

    Route::get('',  [InformationController::class, 'index'])->middleware('perm:ombor.informations.view');
    Route::post('', [InformationController::class, 'store'])->middleware('perm:ombor.informations.create');
    Route::get('{information}', [InformationController::class, 'show'])->middleware('perm:ombor.informations.view');

    Route::post('{information}/accept',   [InformationController::class, 'accept'])->middleware('perm:ombor.informations.view');
    Route::post('{information}/start',    [InformationController::class, 'start'])->middleware('perm:ombor.informations.view');
    Route::post('{information}/complete', [InformationController::class, 'complete'])->middleware('perm:ombor.informations.view');
});





//Warehouse
Route::get('warehouse', [WarehouseController::class, 'index']);
Route::get('warehouse/item-types', [WarehouseController::class, 'itemTypes']); // {id} dan OLDIN turishi shart
Route::post('warehouse', [WarehouseController::class, 'store']);
Route::get('warehouse/{id}', [WarehouseController::class, 'show']);

//warehousetransfer
Route::prefix('warehouse-transfer')->middleware('perm:ombor.warehouse.update')->group(function () {
    Route::get('staff-search', [WarehouseTransferController::class, 'staffSearch']);
    Route::post('',            [WarehouseTransferController::class, 'store']);
});

//Transfer
Route::prefix('transfers')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});
