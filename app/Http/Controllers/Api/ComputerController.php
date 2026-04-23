<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComputerRequest;
use App\Http\Requests\TransferComputerRequest;
use App\Http\Requests\UpdateComputerRequest;
use App\Http\Resources\ComputerResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\ComputerRepositoryInterface;
use App\Services\Contracts\ComputerTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ComputerController extends Controller
{
    public function __construct(
        private readonly ComputerRepositoryInterface      $computers,
        private readonly ComputerTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ComputerResource::collection($this->computers->paginate(15));
    }

    public function show(int $id): ComputerResource
    {
        return new ComputerResource($this->computers->findOrFail($id));
    }

    public function store(StoreComputerRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $computer = $this->computers->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Kompyuter muvaffaqiyatli yaratildi.',
            'data'    => new ComputerResource($computer),
        ], 201);
    }

    public function update(UpdateComputerRequest $request, int $id): JsonResponse
    {
        $computer = $this->computers->findOrFail($id);
        $computer = $this->computers->update($computer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kompyuter muvaffaqiyatli yangilandi.',
            'data'    => new ComputerResource($computer),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $computer = $this->computers->findOrFail($id);
        $this->computers->delete($computer);

        return response()->json([
            'success' => true,
            'message' => 'Kompyuter muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferComputerRequest $request): JsonResponse
    {
        try {
            $computer = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new ComputerResource($computer),
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
