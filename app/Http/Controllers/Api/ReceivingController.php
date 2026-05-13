<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceivingRequest;
use App\Http\Requests\UpdateReceivingRequest;
use App\Http\Resources\ReceivingResource;
use App\Http\Traits\ApiResponse;
use App\Services\ReceivingService;
use Illuminate\Http\JsonResponse;

class ReceivingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ReceivingService $service,
    ) {}

    /**
     * GET /api/v1/receivings?per_page=15
     */
    public function index(): JsonResponse
    {
        $result = $this->service->paginate(request()->integer('per_page', 15));

        return $this->paginated($result, ReceivingResource::class);
    }

    /**
     * GET /api/v1/receivings/all  (pagination yo'q — dropdown uchun)
     */
    public function all(): JsonResponse
    {
        $receivings = $this->service->getAll();

        return $this->success(ReceivingResource::collection($receivings));
    }

    /**
     * POST /api/v1/receivings
     */
    public function store(StoreReceivingRequest $request): JsonResponse
    {
        $receiving = $this->service->create($request->validated());

        return $this->created(new ReceivingResource($receiving));
    }

    /**
     * GET /api/v1/receivings/{id}
     */
    public function show(int $id): JsonResponse
    {
        $receiving = $this->service->findWithItems($id);

        return $this->success(new ReceivingResource($receiving));
    }

    /**
     * PUT /api/v1/receivings/{id}
     */
    public function update(UpdateReceivingRequest $request, int $id): JsonResponse
    {
        $receiving = $this->service->update($id, $request->validated());

        return $this->success(new ReceivingResource($receiving), 'Receiving updated successfully.');
    }

    /**
     * DELETE /api/v1/receivings/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->noContent('Receiving deleted successfully.');
    }
}
