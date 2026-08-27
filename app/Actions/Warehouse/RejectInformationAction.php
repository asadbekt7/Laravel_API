<?php

declare(strict_types=1);

namespace App\Actions\Warehouse;

use App\Models\InformationModel;

final readonly class RejectInformationAction
{
    public function execute(InformationModel $information, string $reason): bool
    {
        // Xuddi shu sababdan - accept bilan reject bir-biriga "poyga qilib"
        // kirib qolmasligi uchun qatorni qulflab o'qiymiz.
        /** @var InformationModel $information */
        $information = InformationModel::query()
            ->whereKey($information->id)
            ->lockForUpdate()
            ->firstOrFail();

        return $information->reject($reason);
    }
}
