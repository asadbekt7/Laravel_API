<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\InventoryNumberController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UnityController;
//use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ComputerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Api\InventoryImportController;
use App\Http\Controllers\Api\UzasboImportController;
use App\Http\Controllers\Api\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('category', [CategoriesController::class, 'index']);
Route::get('category/{id}', [CategoriesController::class, 'show']);
Route::apiResource('model', ModelsController::class);
Route::get('status', [StatusController::class, 'index']);
Route::get('status/{id}', [StatusController::class, 'show']);
Route::get('network', [NetworkController::class, 'index']);
Route::get('network/{id}', [NetworkController::class, 'show']);
Route::apiResource('inventorynumber', InventorynumberController::class);
Route::get('unit', [UnityController::class, 'index']);
Route::get('unit/{id}', [UnityController::class, 'show']);
//Route::prefix('attachments')->group(function () {
//    Route::get('/',            [AttachmentController::class, 'index']);
//    Route::post('/',           [AttachmentController::class, 'store']);
//    Route::delete('/{attachment}', [AttachmentController::class, 'destroy']);
//});
Route::apiResource('warehouse', WarehouseController::class);
Route::post('computers/transfer', [ComputerController::class, 'transfer'])
    ->name('computers.transfer');

Route::apiResource('computers', ComputerController::class);
Route::get('staff', [StaffController::class, 'index']);
Route::post('/inventory/import', InventoryImportController::class)
    ->name('inventory.import');
Route::prefix('uzasbo-imports')->controller(UzasboImportController::class)->group(function () {
    Route::get('/',          'index');
    Route::get('/{id}',      'show');
    Route::post('/transfer', 'transfer');
});
Route::get('/document-types', [DocumentTypeController::class, 'index']);
Route::get('/document-types/{id}', [DocumentTypeController::class, 'show']);
Route::prefix('transfers')->group(function () {
    Route::post('/', [TransferController::class, 'store']);
});
