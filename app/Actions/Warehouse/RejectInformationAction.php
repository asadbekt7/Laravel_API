<?php

declare(strict_types=1);

namespace App\Actions\Warehouse;

use App\Models\InformationModel;

final readonly class RejectInformationAction
{
    public function execute(InformationModel $information, string $reason): bool
    {
        return $information->reject($reason);
    }
}
