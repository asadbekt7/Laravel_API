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
use App\Http\Controllers\Api\BugalteriyaController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\WarehouseBatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
    // Eslatma: oldin 'items/{id}' deb yozilgan edi — bu /items/items/{id} bo'lib qolardi. To'g'irlandi:
    Route::put('/{id}',   [ItemsController::class, 'update']);
    Route::patch('/{id}', [ItemsController::class, 'update']);
});

//Staff Search
Route::get('/staff/search', [StaffController::class, 'search'])
    ->middleware('perm:ombor.staff.view');

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
        Route::get('/',                 'index');   // ?search=... bilan filter
        Route::get('/{roomName}',       'show');    // API + DB birlashgan
        Route::get('/{roomName}/items', 'items');   // faqat DB statistika
    });

//Bugalteriya
Route::prefix('bugalteriya')->group(function () {
    Route::get('/',              [BugalteriyaController::class, 'index'])->middleware('perm:ombor.bugalteriya.view');    // ro'yxat (?status=pending)
    Route::get('/{bugalteriya}', [BugalteriyaController::class, 'show'])->middleware('perm:ombor.bugalteriya.view');     // bitta yozuv
    Route::post('/{bugalteriya}/complete', [BugalteriyaController::class, 'complete'])->middleware('perm:ombor.bugalteriya.complete'); // yakunlash -> items
    Route::post('/{bugalteriya}/cancel',   [BugalteriyaController::class, 'cancel'])->middleware('perm:ombor.bugalteriya.cancel');     // bekor qilish
});

//Information
Route::prefix('information')->group(function () {
    // Statik route'lar {information} parametridan OLDIN turishi shart
    Route::get('pending',  [InformationController::class, 'pending'])->middleware('perm:ombor.informations.view');
    Route::get('my-tasks', [InformationController::class, 'myTasks'])->middleware('perm:ombor.informations.view');
    Route::post('bulk',    [InformationController::class, 'bulkStore'])->middleware('perm:ombor.informations.create');

    Route::get('',  [InformationController::class, 'index'])->middleware('perm:ombor.informations.view');
    Route::post('', [InformationController::class, 'store'])->middleware('perm:ombor.informations.create');
    Route::get('{information}', [InformationController::class, 'show'])->middleware('perm:ombor.informations.view');

    // Holatni o'zgartiruvchi action'lar uchun alohida "update" permission tavsiya etiladi
    Route::post('{information}/accept',   [InformationController::class, 'accept'])->middleware('perm:ombor.informations.update');
    Route::post('{information}/start',    [InformationController::class, 'start'])->middleware('perm:ombor.informations.update');
    Route::post('{information}/complete', [InformationController::class, 'complete'])->middleware('perm:ombor.informations.update');
});

//Warehouse
Route::prefix('warehouse')->group(function () {
    Route::get('/',           [WarehouseController::class, 'index'])->middleware('perm:ombor.warehouse.view');
    Route::get('/item-types', [WarehouseController::class, 'itemTypes'])->middleware('perm:ombor.warehouse.view'); // {id} dan OLDIN turishi shart
    // stats for manage todo middleware larni yoqib qo'yish kerak, loyiha topshirilgandan keyin
    Route::get('/stats/categories', [WarehouseController::class, 'statsCategories']); //->middleware('perm:ombor.warehouse.view');
    Route::get('/stats/models',     [WarehouseController::class, 'statsModels']); //->middleware('perm:ombor.warehouse.view');

    Route::post('/',          [WarehouseController::class, 'store'])->middleware('perm:ombor.warehouse.create');
    Route::get('/{id}',       [WarehouseController::class, 'show'])->middleware('perm:ombor.warehouse.view');
});

//WarehouseTransfer
Route::prefix('warehouse-transfer')->middleware('perm:ombor.warehouse.update')->group(function () {
    Route::get('staff-search', [WarehouseTransferController::class, 'staffSearch']);
    Route::post('',            [WarehouseTransferController::class, 'store']);
});

//Transfer
Route::prefix('transfers')->middleware('perm:ombor.transfers.create')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});

//WarehouseBatch
Route::middleware('auth:sanctum')->group(function () {
    // 1-qadam — batch yaratish
    Route::post('warehouse-batches', [WarehouseBatchController::class, 'store']);
    Route::get('warehouse-batches/{batch}', [WarehouseBatchController::class, 'show']);
    Route::get('warehouse-batches/{batch}/items', [WarehouseBatchController::class, 'items']);

    // Buxgalter va tasdiqlovchilar
    Route::post('warehouse-batches/{batch}/sign', [WarehouseBatchController::class, 'signAsAccountant']); // Menyu 2
    Route::post('warehouse-batches/{batch}/approve', [WarehouseBatchController::class, 'approve']);
    Route::post('warehouse-batches/{batch}/reject', [WarehouseBatchController::class, 'reject']);

    // PDF yuklab olish (signed route)
    Route::get('warehouse-batches/{batch}/pdf', function (\App\Models\WarehouseBatch $batch) {
        abort_unless($batch->file_path && Storage::disk('local')->exists($batch->file_path), 404);

        return Storage::disk('local')->response($batch->file_path);
    })->middleware('signed')->name('warehouse-batches.pdf');
});
//WarehouseTransfer
Route::prefix('warehouse-transfer')->middleware('perm:ombor.warehouse.update')->group(function () {
    Route::get('staff-search', [WarehouseTransferController::class, 'staffSearch']);
    Route::post('',            [WarehouseTransferController::class, 'store']);
});

//Transfer
Route::prefix('transfers')->middleware('perm:ombor.transfers.create')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});

//WarehouseBatch
Route::middleware('auth:sanctum')->group(function () {
    // 1-qadam — batch yaratish
    Route::post('warehouse-batches', [WarehouseBatchController::class, 'store']);
    Route::get('warehouse-batches/{batch}', [WarehouseBatchController::class, 'show']);
    Route::get('warehouse-batches/{batch}/items', [WarehouseBatchController::class, 'items']);

    // Buxgalter va tasdiqlovchilar
    Route::post('warehouse-batches/{batch}/sign', [WarehouseBatchController::class, 'signAsAccountant']); // Menyu 2
    Route::post('warehouse-batches/{batch}/approve', [WarehouseBatchController::class, 'approve']);
    Route::post('warehouse-batches/{batch}/reject', [WarehouseBatchController::class, 'reject']);

    // PDF yuklab olish (signed route)
    Route::get('warehouse-batches/{batch}/pdf', function (\App\Models\WarehouseBatch $batch) {
        abort_unless($batch->file_path && Storage::disk('local')->exists($batch->file_path), 404);

        return Storage::disk('local')->response($batch->file_path);
    })->middleware('signed')->name('warehouse-batches.pdf');
});
