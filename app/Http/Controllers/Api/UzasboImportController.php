<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferUzasboImportRequest;
use App\Models\UzasboImportModel;
use App\Services\UzasboImportTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UzasboImportController extends Controller
{
    public function __construct(
        private readonly UzasboImportTransferService $transferService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = UzasboImportModel::query()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $data->items(),
            'meta'    => [
                'total'        => $data->total(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => UzasboImportModel::findOrFail($id),
        ]);
    }

    public function transfer(TransferUzasboImportRequest $request): JsonResponse
    {
        try {
            $result = $this->transferService->transfer($request);

        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), ['staff_id' => $e->getMessage()], 422);

        } catch (RoomApiException $e) {
            return $this->errorResponse($e->getMessage(), ['room_name' => $e->getMessage()], 422);

        } catch (\RuntimeException $e) {
            return $this->errorResponse('Tashqi servis bilan bog\'lanishda xato yuz berdi.', ['api' => $e->getMessage()], 503);

        } catch (\Throwable $e) {
            return $this->errorResponse('Server xatosi yuz berdi.', [], 500);
        }

        $transferredCount = count($result['transferred']);
        $skippedCount     = count($result['skipped']);

        if ($transferredCount === 0) {
            return $this->errorResponse('Hech qanday yozuv transfer qilinmadi', [
                'transferred_count' => 0,
                'skipped_count'     => $skippedCount,
                'transferred_ids'   => [],
                'skipped'           => $result['skipped'],
            ], 422);
        }

        $status  = $skippedCount > 0 ? 207 : 200;
        $message = $skippedCount > 0 ? 'Transfer qisman yakunlandi' : 'Transfer muvaffaqiyatli yakunlandi';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'transferred_count' => $transferredCount,
                'skipped_count'     => $skippedCount,
                'transferred_ids'   => $result['transferred'],
                'skipped'           => $result['skipped'],
            ],
        ], $status);
    }

    private function errorResponse(string $message, array $errors = [], int $status = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
