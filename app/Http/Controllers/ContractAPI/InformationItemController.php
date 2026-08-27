<?php

declare(strict_types=1);

namespace App\Http\Controllers\ContractAPI;

use App\DTOs\InformationItemData;
use App\Http\Controllers\Controller;
use App\Http\Requests\InformationItem\StoreInformationItemRequest;
use App\Http\Requests\InformationItem\UpdateInformationItemRequest;
use App\Http\Resources\Information\InformationItemResource;
use App\Models\InformationItem;
use App\Models\InformationModel;
use App\Services\InformationItemService;
use Illuminate\Http\JsonResponse;

class InformationItemController extends Controller
{
    public function __construct(private readonly InformationItemService $service)
    {
    }

    public function store(StoreInformationItemRequest $request, InformationModel $information): JsonResponse
    {
        $item = $this->service->add($information, InformationItemData::fromArray($request->validated()));

        return response()->json([
            'message' => "Mahsulot qo'shildi.",
            'data'    => new InformationItemResource($item->load('unit')),
        ], 201);
    }

    public function update(
        UpdateInformationItemRequest $request,
        InformationModel $information,
        InformationItem $item
    ): JsonResponse {
        $merged = array_merge(
            $item->only(['product_name', 'unit_id', 'quantity', 'item_price']),
            $request->validated()
        );

        $item = $this->service->update($item, InformationItemData::fromArray($merged, $item->id));

        return response()->json([
            'message' => 'Mahsulot yangilandi.',
            'data'    => new InformationItemResource($item->load('unit')),
        ]);
    }

    public function destroy(InformationModel $information, InformationItem $item): JsonResponse
    {
        $this->authorize('manageItems', $information);

        abort_if($item->information_id !== $information->id, 404);

        $this->service->delete($item);

        return response()->json(['message' => "Mahsulot o'chirildi."]);
    }
}
