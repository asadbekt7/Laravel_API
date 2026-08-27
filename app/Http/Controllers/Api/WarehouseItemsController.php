<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseItemFilter;
use App\Http\Requests\Warehouse\WarehouseItemsIndexRequest;
use App\Http\Resources\Warehouse\WarehouseItemResource;
use App\Services\WarehouseItemService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseItemsController extends Controller
{
    public function __construct(
        private readonly WarehouseItemService $service,
    ) {
    }

    /**
     * GET /api/warehouse-items?type_id=&category_id=&model_id=&product_name=&sort_by=&sort_dir=
     */
    public function index(WarehouseItemsIndexRequest $request): AnonymousResourceCollection
    {
        $filter = new WarehouseItemFilter($request);

        $items = $this->service->paginate($filter, $filter->getPerPage($request));

        return WarehouseItemResource::collection($items);
    }
}
