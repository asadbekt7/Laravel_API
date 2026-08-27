<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\InformationItem\AddInformationItemAction;
use App\Actions\InformationItem\DeleteInformationItemAction;
use App\Actions\InformationItem\UpdateInformationItemAction;
use App\DTOs\InformationItemData;
use App\Models\InformationItem;
use App\Models\InformationModel;

final readonly class InformationItemService
{
    public function __construct(
        private AddInformationItemAction $addAction,
        private UpdateInformationItemAction $updateAction,
        private DeleteInformationItemAction $deleteAction,
    ) {
    }

    public function add(InformationModel $information, InformationItemData $data): InformationItem
    {
        return $this->addAction->execute($information, $data);
    }

    public function update(InformationItem $item, InformationItemData $data): InformationItem
    {
        return $this->updateAction->execute($item, $data);
    }

    public function delete(InformationItem $item): void
    {
        $this->deleteAction->execute($item);
    }
}
