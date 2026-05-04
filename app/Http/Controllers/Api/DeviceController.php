<?php
// App/Http/Controllers/Api/DeviceController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\TransferDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Services\Contracts\DeviceTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeviceController extends Controller
{
    public function __construct(
        private readonly DeviceRepositoryInterface      $devices,
        private readonly DeviceTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return DeviceResource::collection($this->devices->paginate(15));
    }

    public function show(int $id): DeviceResource
    {
        return new DeviceResource($this->devices->findOrFail($id));
    }

    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $device = $this->devices->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Device muvaffaqiyatli yaratildi.',
            'data'    => new DeviceResource($device),
        ], 201);
    }

    public function update(UpdateDeviceRequest $request, int $id): JsonResponse
    {
        $device = $this->devices->findOrFail($id);
        $device = $this->devices->update($device, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Device muvaffaqiyatli yangilandi.',
            'data'    => new DeviceResource($device),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $device = $this->devices->findOrFail($id);
        $this->devices->delete($device);

        return response()->json([
            'success' => true,
            'message' => 'Device muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferDeviceRequest $request): JsonResponse
    {
        try {
            $device = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new DeviceResource($device),
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
