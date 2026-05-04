<?php
// App/Http/Controllers/Api/MonitorController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMonitorRequest;
use App\Http\Requests\TransferMonitorRequest;
use App\Http\Requests\UpdateMonitorRequest;
use App\Http\Resources\MonitorResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use App\Services\Contracts\MonitorTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MonitorController extends Controller
{
    public function __construct(
        private readonly MonitorRepositoryInterface      $monitors,
        private readonly MonitorTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return MonitorResource::collection($this->monitors->paginate(15));
    }

    public function show(int $id): MonitorResource
    {
        return new MonitorResource($this->monitors->findOrFail($id));
    }

    public function store(StoreMonitorRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $monitor = $this->monitors->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Monitor muvaffaqiyatli yaratildi.',
            'data'    => new MonitorResource($monitor),
        ], 201);
    }

    public function update(UpdateMonitorRequest $request, int $id): JsonResponse
    {
        $monitor = $this->monitors->findOrFail($id);
        $monitor = $this->monitors->update($monitor, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Monitor muvaffaqiyatli yangilandi.',
            'data'    => new MonitorResource($monitor),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $monitor = $this->monitors->findOrFail($id);
        $this->monitors->delete($monitor);

        return response()->json([
            'success' => true,
            'message' => 'Monitor muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferMonitorRequest $request): JsonResponse
    {
        try {
            $monitor = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new MonitorResource($monitor),
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
