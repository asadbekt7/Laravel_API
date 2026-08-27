<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Request maydoni prefiksi => Storage papka nomi.
 * Masalan: contract_file => informations/contracts
 */
final class InformationFileFields
{
    public const array MAP = [
        'contract'      => 'contracts',
        'bildirishnoma' => 'bildirishnomalar',
        'ishonchnoma'   => 'ishonchnomalar',
        'hisob_faktura' => 'hisob_fakturalar',
    ];
}
