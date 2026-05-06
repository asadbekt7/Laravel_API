<?php

namespace App\Exceptions\ExternalApi;

use Throwable;

/**
 * Thrown when a network/connection error occurs (timeout, DNS failure, etc.)
 */
class ApiConnectionException extends ExternalApiException
{
    public function __construct(
        string $apiName,
        string $url,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            apiName: $apiName,
            message: "Could not connect to [{$url}].",
            statusCode: null,
            responseData: null,
            previous: $previous,
        );
    }
}
