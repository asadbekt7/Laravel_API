<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\InventoryNumberController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UnityController;
//use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ComputerController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\NetworkDeviceController;
use App\Http\Controllers\Api\PrinterController;
use App\Http\Controllers\Api\TelephoneController;
use App\Http\Controllers\Api\TouchpanelController;
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
Route::post('devices/transfer', [DeviceController::class, 'transfer'])
    ->name('devices.transfer');
Route::apiResource('devices', DeviceController::class);
// Monitors
Route::post('monitors/transfer', [MonitorController::class, 'transfer'])
    ->name('monitors.transfer');
Route::apiResource('monitors', MonitorController::class);

// Network Devices
Route::post('network-devices/transfer', [NetworkDeviceController::class, 'transfer'])
    ->name('network-devices.transfer');
Route::apiResource('network-devices', NetworkDeviceController::class);

// Printers
Route::post('printers/transfer', [PrinterController::class, 'transfer'])
    ->name('printers.transfer');
Route::apiResource('printers', PrinterController::class);

// Telephones
Route::post('telephones/transfer', [TelephoneController::class, 'transfer'])
    ->name('telephones.transfer');
Route::apiResource('telephones', TelephoneController::class);

// Touchpanels
Route::post('touchpanels/transfer', [TouchpanelController::class, 'transfer'])
    ->name('touchpanels.transfer');
Route::apiResource('touchpanels', TouchpanelController::class);
