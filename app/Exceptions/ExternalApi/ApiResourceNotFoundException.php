<?php

namespace App\Exceptions\ExternalApi;

use Throwable;

/**
 * Thrown when the API returns 404 for a specific resource.
 */
class ApiResourceNotFoundException extends ExternalApiException
{
    public function __construct(
        string $apiName,
        string $resource,
        int|string $id,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            apiName: $apiName,
            message: "{$resource} with id [{$id}] not found.",
            statusCode: 404,
            responseData: null,
            previous: $previous,
        );
    }
}
