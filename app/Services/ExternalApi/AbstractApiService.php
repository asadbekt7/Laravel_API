<?php

namespace App\Services\ExternalApi;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\ExternalApi\ApiResourceNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractApiService
{
    /**
     * Human-readable API name used in logs and exceptions.
     */
    abstract protected function apiName(): string;

    /**
     * Base URL read from .env, e.g. "https://api.staff.example.com/v1"
     */
    abstract protected function baseUrl(): string;

    /**
     * Default query parameters appended to every request (override as needed).
     */
    protected function defaultQueryParams(): array
    {
        return [];
    }

    /**
     * Default headers appended to every request (override as needed).
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Timeout in seconds for the HTTP request. Override to change.
     */
    protected function timeoutSeconds(): int
    {
        return 30;
    }

    // -------------------------------------------------------------------------
    // Public API — used by child services
    // -------------------------------------------------------------------------

    /**
     * GET /endpoint  →  returns the full decoded array.
     *
     * @param  array<string,mixed>  $query  Extra query-string parameters.
     * @return array<mixed>
     */
    protected function getList(string $endpoint, array $query = []): array
    {
        $url      = $this->buildUrl($endpoint);
        $response = $this->send('GET', $url, array_merge($this->defaultQueryParams(), $query));

        return $this->decodeArray($response, $url);
    }

    /**
     * GET /endpoint/{id}  →  returns the full decoded array for that resource.
     *
     * @return array<mixed>
     */
    protected function getById(string $endpoint, int|string $id, array $query = []): array
    {
        $url      = $this->buildUrl("{$endpoint}/{$id}");
        $response = $this->send('GET', $url, array_merge($this->defaultQueryParams(), $query));

        if ($response->status() === 404) {
            throw new ApiResourceNotFoundException(
                apiName: $this->apiName(),
                resource: class_basename(static::class),
                id: $id,
            );
        }

        return $this->decodeArray($response, $url);
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    private function buildUrl(string $endpoint): string
    {
        return rtrim($this->baseUrl(), '/') . '/' . ltrim($endpoint, '/');
    }

    private function httpClient(): PendingRequest
    {
        return Http::withHeaders($this->defaultHeaders())
            ->timeout($this->timeoutSeconds())
            ->acceptJson();
    }

    /**
     * Dispatch the HTTP request and handle low-level errors.
     */
    private function send(string $method, string $url, array $query = []): Response
    {
        try {
            $request = $this->httpClient();

            /** @var Response $response */
            $response = match (strtoupper($method)) {
                'GET' => $request->get($url, $query),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            $this->logResponse($method, $url, $response);

            if ($response->failed() && $response->status() !== 404) {
                throw new ApiRequestFailedException(
                    apiName: $this->apiName(),
                    statusCode: $response->status(),
                    url: $url,
                    responseData: $this->safeDecode($response),
                );
            }

            return $response;

        } catch (ConnectionException $e) {
            Log::error("[{$this->apiName()}] Connection failed", [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);

            throw new ApiConnectionException(
                apiName: $this->apiName(),
                url: $url,
                previous: $e,
            );
        }
    }

    /**
     * Decode JSON response to array; throw if not decodable or not an array.
     *
     * @return array<mixed>
     */
    private function decodeArray(Response $response, string $url): array
    {
        $data = $response->json();

        if (!is_array($data)) {
            throw new ApiInvalidResponseException(
                apiName: $this->apiName(),
                url: $url,
                reason: 'Response is not a JSON array/object.',
            );
        }

        return $data;
    }

    /**
     * Try to JSON-decode for logging; return null on failure.
     *
     * @return array<mixed>|null
     */
    private function safeDecode(Response $response): ?array
    {
        try {
            $data = $response->json();
            return is_array($data) ? $data : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function logResponse(string $method, string $url, Response $response): void
    {
        $level   = $response->successful() ? 'debug' : 'warning';
        $context = [
            'api'    => $this->apiName(),
            'method' => $method,
            'url'    => $url,
            'status' => $response->status(),
        ];

        Log::log($level, "[{$this->apiName()}] {$method} {$url} → {$response->status()}", $context);
    }
}
