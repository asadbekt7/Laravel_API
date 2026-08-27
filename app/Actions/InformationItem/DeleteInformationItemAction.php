<?php
// app/Actions/InformationItem/DeleteInformationItemAction.php
declare(strict_types=1);

namespace App\Actions\InformationItem;

use App\Models\InformationItem;

final readonly class DeleteInformationItemAction
{
    public function execute(InformationItem $item): void
    {
        $item->delete();
    }
}
