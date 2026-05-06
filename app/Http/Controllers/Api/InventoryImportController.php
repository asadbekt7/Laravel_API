<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryImportRequest;
use App\Imports\InventoryImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class InventoryImportController extends Controller
{
    public function __invoke(InventoryImportRequest $request): JsonResponse
    {
        try {
            $importer = new InventoryImport($request->input('import_type'));

            Excel::import($importer, $request->file('file'));

            $errors   = $importer->getErrors();
            $imported = $importer->getImportedCount();
            $skipped  = $importer->getSkippedCount();

            // Log summary for audit trail
            Log::info('InventoryImport completed', [
                'imported'    => $imported,
                'skipped'     => $skipped,
                'error_count' => count($errors),
                'file'        => $request->file('file')->getClientOriginalName(),
                'import_type' => $request->input('import_type'),
                'user_id'     => auth()->id(),
            ]);

            return response()->json([
                'success'  => true,
                'imported' => $imported,
                'skipped'  => $skipped,
                'errors'   => $errors,
            ]);

        } catch (Throwable $e) {
            Log::error('InventoryImport failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import jarayonida xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
