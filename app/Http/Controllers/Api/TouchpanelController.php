<?php
// App/Http/Controllers/Api/TouchpanelController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTouchpanelRequest;
use App\Http\Requests\TransferTouchpanelRequest;
use App\Http\Requests\UpdateTouchpanelRequest;
use App\Http\Resources\TouchpanelResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\TouchpanelRepositoryInterface;
use App\Services\Contracts\TouchpanelTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TouchpanelController extends Controller
{
    public function __construct(
        private readonly TouchpanelRepositoryInterface      $touchpanels,
        private readonly TouchpanelTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TouchpanelResource::collection($this->touchpanels->paginate(15));
    }

    public function show(int $id): TouchpanelResource
    {
        return new TouchpanelResource($this->touchpanels->findOrFail($id));
    }

    public function store(StoreTouchpanelRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $touchpanel = $this->touchpanels->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Touchpanel muvaffaqiyatli yaratildi.',
            'data'    => new TouchpanelResource($touchpanel),
        ], 201);
    }

    public function update(UpdateTouchpanelRequest $request, int $id): JsonResponse
    {
        $touchpanel = $this->touchpanels->findOrFail($id);
        $touchpanel = $this->touchpanels->update($touchpanel, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Touchpanel muvaffaqiyatli yangilandi.',
            'data'    => new TouchpanelResource($touchpanel),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $touchpanel = $this->touchpanels->findOrFail($id);
        $this->touchpanels->delete($touchpanel);

        return response()->json([
            'success' => true,
            'message' => 'Touchpanel muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferTouchpanelRequest $request): JsonResponse
    {
        try {
            $touchpanel = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new TouchpanelResource($touchpanel),
            ], 201);

        } catch (WarehouseNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (RoomApiException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 503);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bu inventar raqami allaqachon mavjud.',
                'errors'  => ['inventory_number' => ['Bu inventar raqami allaqachon mavjud.']],
            ], 422);
        } catch (TransferFailedException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
