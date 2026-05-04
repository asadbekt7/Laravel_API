<?php
// App/Http/Controllers/Api/TelephoneController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTelephoneRequest;
use App\Http\Requests\TransferTelephoneRequest;
use App\Http\Requests\UpdateTelephoneRequest;
use App\Http\Resources\TelephoneResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\TelephoneRepositoryInterface;
use App\Services\Contracts\TelephoneTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TelephoneController extends Controller
{
    public function __construct(
        private readonly TelephoneRepositoryInterface      $telephones,
        private readonly TelephoneTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TelephoneResource::collection($this->telephones->paginate(15));
    }

    public function show(int $id): TelephoneResource
    {
        return new TelephoneResource($this->telephones->findOrFail($id));
    }

    public function store(StoreTelephoneRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $telephone = $this->telephones->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Telefon muvaffaqiyatli yaratildi.',
            'data'    => new TelephoneResource($telephone),
        ], 201);
    }

    public function update(UpdateTelephoneRequest $request, int $id): JsonResponse
    {
        $telephone = $this->telephones->findOrFail($id);
        $telephone = $this->telephones->update($telephone, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Telefon muvaffaqiyatli yangilandi.',
            'data'    => new TelephoneResource($telephone),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $telephone = $this->telephones->findOrFail($id);
        $this->telephones->delete($telephone);

        return response()->json([
            'success' => true,
            'message' => 'Telefon muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferTelephoneRequest $request): JsonResponse
    {
        try {
            $telephone = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new TelephoneResource($telephone),
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
