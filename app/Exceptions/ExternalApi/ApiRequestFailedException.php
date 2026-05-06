<?php

namespace App\Exceptions\ExternalApi;

use Throwable;

/**
 * Thrown when the external API returns a non-2xx HTTP response.
 */
class ApiRequestFailedException extends ExternalApiException
{
    public function __construct(
        string $apiName,
        int $statusCode,
        string $url,
        ?array $responseData = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            apiName: $apiName,
            message: "Request to [{$url}] failed.",
            statusCode: $statusCode,
            responseData: $responseData,
            previous: $previous,
        );
    }
}
