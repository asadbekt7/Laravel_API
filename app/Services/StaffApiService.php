<?php

namespace App\Services;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StaffApiService
{
    private const API_NAME = 'StaffAPI';

    private string $baseUrl;
    private string $token;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.staff_api.base_url'), '/');
        $this->token   = config('services.staff_api.api_key');
        $this->timeout = (int) config('services.staff_api.timeout', 30);
    }

    /**
     * Search staff members by name fragment.
     *
     * @return array<int, array{
     *     last_name: string,
     *     first_name: string,
     *     middle_name: string|null,
     *     full_name: string,
     *     department: string|null,
     * }>
     *
     * @throws ApiConnectionException
     * @throws ApiRequestFailedException
     * @throws ApiInvalidResponseException
     */
    public function searchStaff(string $search): array
    {
        $url = "{$this->baseUrl}/api/staff/v1/all/staff";

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->token)
                ->get($url, ['search' => $search]);
        } catch (ConnectionException $e) {
            Log::warning('Staff API: connection failed.', [
                'url'     => $url,
                'search'  => $search,
                'message' => $e->getMessage(),
            ]);

            throw new ApiConnectionException(
                apiName: self::API_NAME,
                url: $url,
                previous: $e,
            );
        }

        if ($response->failed()) {
            Log::error('Staff API: request returned a non-2xx status.', [
                'url'    => $url,
                'search' => $search,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new ApiRequestFailedException(
                apiName: self::API_NAME,
                statusCode: $response->status(),
                url: $url,
                responseData: $response->json() ?? null,
            );
        }

        $body = $response->json();

        if (! is_array($body)) {
            Log::error('Staff API: response body is not a JSON array.', [
                'url'    => $url,
                'search' => $search,
                'body'   => $response->body(),
            ]);

            throw new ApiInvalidResponseException(
                apiName: self::API_NAME,
                url: $url,
                reason: 'Response body is not a valid JSON array.',
            );
        }

        // Javob strukturasi: { success, data: { data: [...] } }
        $items = $body['data']['data'] ?? null;

        if (! is_array($items)) {
            Log::error('Staff API: data.data not found or not an array.', [
                'url'    => $url,
                'search' => $search,
                'body'   => $body,
            ]);

            throw new ApiInvalidResponseException(
                apiName: self::API_NAME,
                url: $url,
                reason: 'Expected data.data to be an array.',
            );
        }

        return $this->parseItems($items, $url, $search);
    }

    /**
     * @param  array<mixed>  $items
     * @return array<int, array{
     *     last_name: string,
     *     first_name: string,
     *     middle_name: string|null,
     *     full_name: string,
     *     department: string|null,
     * }>
     *
     * @throws ApiInvalidResponseException
     */
    private function parseItems(array $items, string $url, string $search): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                Log::error('Staff API: item is not an array.', [
                    'url'    => $url,
                    'search' => $search,
                    'index'  => $index,
                ]);

                throw new ApiInvalidResponseException(
                    apiName: self::API_NAME,
                    url: $url,
                    reason: "Item at index [{$index}] is not an array.",
                );
            }

            // API camelCase qaytaradi: lastName, firstName, middleName
            foreach (['lastName', 'firstName', 'full_name'] as $field) {
                if (! isset($item[$field]) || ! is_string($item[$field])) {
                    Log::error("Staff API: item missing required field '{$field}'.", [
                        'url'    => $url,
                        'search' => $search,
                        'index'  => $index,
                    ]);

                    throw new ApiInvalidResponseException(
                        apiName: self::API_NAME,
                        url: $url,
                        reason: "Item at index [{$index}] is missing required field '{$field}'.",
                    );
                }
            }

            // departments massividan birinchi ACTIVE bo'limni olish
            $department = null;
            if (! empty($item['departments']) && is_array($item['departments'])) {
                foreach ($item['departments'] as $dept) {
                    if (isset($dept['status']) && $dept['status'] === 'ACTIVE') {
                        $department = $dept['departmentName'] ?? null;
                        break;
                    }
                }
            }

            $result[] = [
                'id'          => isset($item['id']) ? (string) $item['id'] : null,
                'last_name'   => $item['lastName'],
                'first_name'  => $item['firstName'],
                'middle_name' => isset($item['middleName']) && is_string($item['middleName'])
                    ? $item['middleName']
                    : null,
                'full_name'   => $item['full_name'],
                'department'  => $department,
            ];
        }

        return $result;
    }
}
