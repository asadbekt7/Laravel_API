<?php

declare(strict_types=1);

namespace App\Actions\Information;

use App\Models\InformationModel;

final readonly class DeleteInformationAction
{
    public function execute(InformationModel $information): void
    {
        // SoftDeletes ishlatilyapti - fayllar diskda qoladi (audit uchun),
        // faqat forceDelete() bosqichida haqiqatan o'chirish tavsiya etiladi.
        $information->delete();
    }
}
