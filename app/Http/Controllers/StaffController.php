<?php

namespace App\Http\Controllers;

use App\Exceptions\ExternalApi\ApiResourceNotFoundException;
use App\Exceptions\ExternalApi\ExternalApiException;
use App\Services\StaffApiService;
use Illuminate\Http\JsonResponse;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffApiService $staffApi,
    ) {}

    /**
     * GET /api/staff
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->staffApi->getAllStaff();

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);

        } catch (ExternalApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * GET /api/staff/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->staffApi->getStaffById($id);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);

        } catch (ApiResourceNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (ExternalApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
