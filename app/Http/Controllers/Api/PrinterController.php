<?php
// App/Http/Controllers/Api/PrinterController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrinterRequest;
use App\Http\Requests\TransferPrinterRequest;
use App\Http\Requests\UpdatePrinterRequest;
use App\Http\Resources\PrinterResource;
use App\Models\Inventorynumbermodel;
use App\Repositories\Contracts\PrinterRepositoryInterface;
use App\Services\Contracts\PrinterTransferServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrinterController extends Controller
{
    public function __construct(
        private readonly PrinterRepositoryInterface      $printers,
        private readonly PrinterTransferServiceInterface $transferService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return PrinterResource::collection($this->printers->paginate(15));
    }

    public function show(int $id): PrinterResource
    {
        return new PrinterResource($this->printers->findOrFail($id));
    }

    public function store(StorePrinterRequest $request): JsonResponse
    {
        $inventoryNumber = Inventorynumbermodel::create([
            'inventory_number' => $request->integer('inventory_number'),
        ]);

        $printer = $this->printers->create(
            array_merge(
                $request->safe()->except('inventory_number'),
                ['inventory_number_id' => $inventoryNumber->id]
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Printer muvaffaqiyatli yaratildi.',
            'data'    => new PrinterResource($printer),
        ], 201);
    }

    public function update(UpdatePrinterRequest $request, int $id): JsonResponse
    {
        $printer = $this->printers->findOrFail($id);
        $printer = $this->printers->update($printer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Printer muvaffaqiyatli yangilandi.',
            'data'    => new PrinterResource($printer),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $printer = $this->printers->findOrFail($id);
        $this->printers->delete($printer);

        return response()->json([
            'success' => true,
            'message' => 'Printer muvaffaqiyatli o\'chirildi.',
        ]);
    }

    public function transfer(TransferPrinterRequest $request): JsonResponse
    {
        try {
            $printer = $this->transferService->transfer(
                warehouseId:     $request->integer('warehouse_id'),
                inventoryNumber: $request->integer('inventory_number'),
                roomName:        $request->string('room_name')->toString(),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mahsulot muvaffaqiyatli ko\'chirildi.',
                'data'    => new PrinterResource($printer),
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
