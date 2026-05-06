<?php

namespace App\Exceptions\ExternalApi;

use Throwable;

/**
 * Thrown when the API response cannot be parsed or has unexpected structure.
 */
class ApiInvalidResponseException extends ExternalApiException
{
    public function __construct(
        string $apiName,
        string $url,
        string $reason = 'Unexpected response structure.',
        ?Throwable $previous = null
    ) {
        parent::__construct(
            apiName: $apiName,
            message: "Invalid response from [{$url}]: {$reason}",
            statusCode: null,
            responseData: null,
            previous: $previous,
        );
    }
}
