<?php

namespace App\Exceptions\ExternalApi;

use RuntimeException;
use Throwable;

class ExternalApiException extends RuntimeException
{
    protected string $apiName;
    protected ?array $responseData;
    protected ?int $statusCode;

    public function __construct(
        string $apiName,
        string $message = '',
        ?int $statusCode = null,
        ?array $responseData = null,
        ?Throwable $previous = null
    ) {
        $this->apiName      = $apiName;
        $this->statusCode   = $statusCode;
        $this->responseData = $responseData;

        $fullMessage = "[{$apiName}] {$message}";
        if ($statusCode !== null) {
            $fullMessage .= " (HTTP {$statusCode})";
        }

        parent::__construct($fullMessage, $statusCode ?? 0, $previous);
    }

    public function getApiName(): string
    {
        return $this->apiName;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public function context(): array
    {
        return [
            'api_name'      => $this->apiName,
            'status_code'   => $this->statusCode,
            'response_data' => $this->responseData,
        ];
    }
}
