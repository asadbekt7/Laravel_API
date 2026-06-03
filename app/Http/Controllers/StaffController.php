<?php

namespace App\Http\Controllers;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Services\StaffApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffApiService $staffApi,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) ($request->input('q') ?? $request->input('search') ?? ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data'    => [],
            ]);
        }

        try {
            $results = $this->staffApi->searchStaff($query);
        } catch (ApiConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimiga ulanib bo\'lmadi. Qayta urinib ko\'ring.',
            ], 503);

        } catch (ApiRequestFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimi xato javob qaytardi.',
            ], 502);

        } catch (ApiInvalidResponseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xodimlar tizimidan kutilmagan javob keldi.',
            ], 502);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Server xatoligi yuz berdi.',
            ], 500);
        }

        $items = array_map(function (array $r) {
            return [
                'id'          => $r['id']          ?? null,
                'lastName'    => $r['last_name']   ?? '',
                'firstName'   => $r['first_name']  ?? '',
                'middleName'  => $r['middle_name'] ?? null,
                'full_name'   => $r['full_name']   ?? '',
                'department'  => $r['department']  ?? null,
            ];
        }, $results);

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }
}
