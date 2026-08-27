<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Information\ChangeInformationStatusAction;
use App\Actions\Information\CreateInformationAction;
use App\Actions\Information\DeleteInformationAction;
use App\Actions\Information\UpdateInformationAction;
use App\Enums\InformationStatus;
use App\Models\InformationModel;
use App\Repositories\InformationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final readonly class InformationService
{
    public function __construct(
        private InformationRepositoryInterface $repository,
        private CreateInformationAction $createAction,
        private UpdateInformationAction $updateAction,
        private DeleteInformationAction $deleteAction,
        private ChangeInformationStatusAction $statusAction,
    ) {
    }

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id): InformationModel
    {
        return $this->repository->findOrFailWithRelations($id);
    }

    public function create(array $validated, array $items): InformationModel
    {
        return DB::transaction(fn () => $this->createAction->execute($validated, $items));
    }

    public function update(InformationModel $information, array $validated, ?array $items): InformationModel
    {
        return DB::transaction(fn () => $this->updateAction->execute($information, $validated, $items));
    }

    public function delete(InformationModel $information): void
    {
        DB::transaction(fn () => $this->deleteAction->execute($information));
    }

    public function accept(InformationModel $information): bool
    {
        return $this->statusAction->execute($information, InformationStatus::Accepted);
    }

    public function start(InformationModel $information): bool
    {
        return $this->statusAction->execute($information, InformationStatus::InProgress);
    }

    public function complete(InformationModel $information): bool
    {
        return $this->statusAction->execute($information, InformationStatus::Completed);
    }
}
