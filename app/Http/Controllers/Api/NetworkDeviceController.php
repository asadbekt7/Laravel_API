<?php
// App/Http/Controllers/Api/NetworkDeviceController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNetworkDeviceRequest;
use App\Http\Requests\TransferNetworkDeviceRequest;
use App\Http\Requests\UpdateNetworkDeviceRequest;
use App\Http\Resources\NetworkDeviceResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\NetworkDeviceRepositoryInterface;
use App\Services\Contracts\NetworkDeviceTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NetworkDeviceController extends Controller
{
    public function __construct(
        private readonly NetworkDeviceRepositoryInterface      $networkDevices,
        private readonly NetworkDeviceTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return NetworkDeviceResource::collection($this->networkDevices->paginate(15));
    }

    public function show(int $id): NetworkDeviceResource
    {
        return new NetworkDeviceResource($this->networkDevices->findOrFail($id));
    }

    public function store(StoreNetworkDeviceRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $networkDevice = $this->networkDevices->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Network device muvaffaqiyatli yaratildi.',
            'data'    => new NetworkDeviceResource($networkDevice),
        ], 201);
    }

    public function update(UpdateNetworkDeviceRequest $request, int $id): JsonResponse
    {
        $networkDevice = $this->networkDevices->findOrFail($id);
        $networkDevice = $this->networkDevices->update($networkDevice, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Network device muvaffaqiyatli yangilandi.',
            'data'    => new NetworkDeviceResource($networkDevice),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $networkDevice = $this->networkDevices->findOrFail($id);
        $this->networkDevices->delete($networkDevice);

        return response()->json([
            'success' => true,
            'message' => 'Network device muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferNetworkDeviceRequest $request): JsonResponse
    {
        try {
            $networkDevice = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new NetworkDeviceResource($networkDevice),
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
