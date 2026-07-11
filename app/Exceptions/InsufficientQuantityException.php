<?php

namespace App\Exceptions;

use Exception;

class InsufficientQuantityException extends Exception
{
    public function __construct(
        public readonly string $productName,
        public readonly int $available,
        public readonly int $requested,
    ) {
        $message = $available === 0
            ? "\"{$productName}\" ombordan qolmadi."
            : "\"{$productName}\" yetarli emas. Omborda: {$available} ta, so'raldi: {$requested} ta.";

        parent::__construct($message);
    }
}
