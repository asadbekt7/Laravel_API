<?php

declare(strict_types=1);

namespace App\Actions\Information;

use App\Enums\InformationStatus;
use App\Models\InformationModel;

final readonly class ChangeInformationStatusAction
{
    public function execute(InformationModel $information, InformationStatus $target): bool
    {
        return $information->transitionTo($target);
    }
}
