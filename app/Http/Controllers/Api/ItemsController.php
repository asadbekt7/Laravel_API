<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\RoomApiException;
use App\Http\Requests\Items\UpdateItemStaffRequest;
use App\Services\ItemService\EditItemStaffService;
use App\Http\Controllers\Controller;
use App\Models\ItemsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function __construct(
        protected EditItemStaffService $editItemStaffService,
    ) {}
    /**
     * GET /api/items
     */
    public function index(Request $request): JsonResponse
    {
        $query = ItemsModel::with(['unit', 'type', 'category', 'model', 'uzasboImport']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->filled('building')) {
            $query->where('building', $request->building);
        }

        if ($request->filled('room_number')) {
            $query->where('room_number', $request->room_number);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', '%' . $request->full_name . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('inventory_number', 'ILIKE', "%{$search}%")
                    ->orWhere('full_name', 'ILIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $items   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    /**
     * GET /api/items/{id}
     */
    public function show(int $id): JsonResponse
    {
        $item = ItemsModel::with(['unit', 'type', 'category', 'model', 'uzasboImport'])
            ->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item topilmadi.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $item,
        ]);
    }
    /**
     * PUT /api/items/{id}/staff
     */
    public function update(UpdateItemStaffRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->editItemStaffService->edit(
                itemIds: [$id],
                params:  $request->params(),
            );

            // Bitta item — natijani tekshirib, mos javob qaytaramiz
            if (! empty($result['errors'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['errors'][0]['error'] ?? 'Tahrirlashda xatolik yuz berdi.',
                    'data'    => $result,
                ], 422);
            }

            if (! empty($result['skipped'])) {
                return response()->json([
                    'success' => true,
                    'message' => $result['skipped'][0]['reason'] ?? 'Hech narsa o\'zgartirilmadi.',
                    'data'    => $result,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item muvaffaqiyatli yangilandi.',
                'data'    => $result,
            ], 200);

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xona ma\'lumotlarini olishda xatolik: ' . $e->getMessage(),
            ], 502);

        } catch (ApiConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodim API ga ulanishda xatolik: ' . $e->getMessage(),
            ], 502);

        } catch (ApiRequestFailedException | ApiInvalidResponseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodim API xatosi: ' . $e->getMessage(),
            ], 502);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kutilmagan xatolik yuz berdi.',
            ], 500);
        }
    }

    /**
     * PUT /api/items/staff/bulk
     */
    public function bulkUpdate(UpdateItemStaffRequest $request): JsonResponse
    {
        $itemIds = $request->itemIds();

        if (empty($itemIds)) {
            return response()->json([
                'success' => false,
                'message' => 'item_ids bo\'sh bo\'lmasligi shart.',
            ], 422);
        }

        try {
            $result = $this->editItemStaffService->edit(
                itemIds: $itemIds,
                params:  $request->params(),
            );

            $hasErrors   = ! empty($result['errors']);
            $hasUpdated  = ! empty($result['updated']);

            $status  = $hasErrors && ! $hasUpdated ? 422 : 200;
            $success = $hasUpdated || ! $hasErrors;

            $message = match (true) {
                $hasUpdated && ! $hasErrors  => 'Barcha itemlar muvaffaqiyatli yangilandi.',
                $hasUpdated && $hasErrors    => 'Qisman yangilandi. Ba\'zi itemlarda xatolik yuz berdi.',
                ! $hasUpdated && $hasErrors  => 'Hech bir item yangilanmadi. Barcha itemlarda xatolik.',
                default                      => 'Hech narsa o\'zgartirilmadi.',
            };

            return response()->json([
                'success' => $success,
                'message' => $message,
                'data'    => $result,
            ], $status);

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xona ma\'lumotlarini olishda xatolik: ' . $e->getMessage(),
            ], 502);

        } catch (ApiConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodim API ga ulanishda xatolik: ' . $e->getMessage(),
            ], 502);

        } catch (ApiRequestFailedException | ApiInvalidResponseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodim API xatosi: ' . $e->getMessage(),
            ], 502);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kutilmagan xatolik yuz berdi.',
            ], 500);
        }
    }
}
