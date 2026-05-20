<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\RoomApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfers\TransferImportRequest;
use App\Models\UzasboImportModel;
use App\Services\UzasboImportTransferService;
use Illuminate\Http\JsonResponse;

class UzasboImportController extends Controller
{
    public function __construct(
        protected UzasboImportTransferService $transferService,
    ) {}

    /**
     * GET /api/uzasbo-imports
     * status = null yoki 'transfered' bo'lmaganlarni qaytaradi
     */
    public function index(): JsonResponse
    {
        $imports = UzasboImportModel::where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '!=', 'transfered');
        })
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $imports,
            'total'   => $imports->count(),
        ]);
    }

    /**
     * GET /api/uzasbo-imports/{id}
     * status = null yoki 'transfered' bo'lmagan bitta yozuv
     */
    public function show(int $id): JsonResponse
    {
        $import = UzasboImportModel::where('id', $id)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'transfered');
            })
            ->first();

        if (! $import) {
            return response()->json([
                'success' => false,
                'message' => 'Yozuv topilmadi yoki allaqachon transfer qilingan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $import,
        ]);
    }

    /**
     * POST /api/uzasbo-imports/transfer
     */
    public function transfer(TransferImportRequest $request): JsonResponse
    {
        try {
            $result = $this->transferService->transfer(
                importIds: $request->validated('import_ids'),
                params: $request->only([
                    'type_id',
                    'category_id',
                    'model_id',
                    'room_name',
                    'last_name',
                ]),
            );

            return $this->buildResponse($result);

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xona ma\'lumotlarini olishda xatolik: ' . $e->getMessage(),
            ], 422);

        } catch (ApiConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimiga ulanib bo\'lmadi. Qayta urinib ko\'ring.',
            ], 503);

        } catch (ApiRequestFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimi xato javob qaytardi.',
            ], 502);

        } catch (ApiInvalidResponseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimidan kutilmagan javob keldi.',
            ], 502);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Server xatoligi yuz berdi.',
            ], 500);
        }
    }

    private function buildResponse(array $result): JsonResponse
    {
        $hasTransferred = ! empty($result['transferred']);
        $hasErrors      = ! empty($result['errors']);

        if ($hasTransferred && $hasErrors) {
            return response()->json([
                'success' => true,
                'message' => "Qisman transfer: {$result['summary']['transferred']} ta ko'chirildi, {$result['summary']['errors']} ta xatolik.",
                'data'    => $result,
            ], 207);
        }

        if ($hasTransferred) {
            return response()->json([
                'success' => true,
                'message' => "{$result['summary']['transferred']} ta yozuv muvaffaqiyatli transfer qilindi.",
                'data'    => $result,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Hech qanday yozuv transfer qilinmadi.',
            'data'    => $result,
        ], 422);
    }
}
