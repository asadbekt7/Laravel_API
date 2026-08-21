<?php

declare(strict_types=1);

namespace App\Services\Receiving;

use App\DTO\Receiving\ReceivingActData;
use App\Exceptions\ReceivingActNotFoundException;
use App\Http\Requests\Receiving\ReceivingIndexRequest;
use App\Repositories\Receiving\ReceivingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ReceivingService
{
    public function __construct(
        private readonly ReceivingRepository $repository,
    ) {
    }

    public function list(ReceivingIndexRequest $request): LengthAwarePaginator
    {
        return $this->repository->paginateGrouped(
            search: $request->string('search')->toString() ?: null,
            supplierId: $request->integer('supplier_id') ?: null,
            fromDate: $request->date('from_date')?->toDateString(),
            toDate: $request->date('to_date')?->toDateString(),
            perPage: (int) ($request->integer('per_page') ?: 15),
        );
    }

    /**
     * @throws ReceivingActNotFoundException
     */
    public function show(string $aktNumber): ReceivingActData
    {
        $rows = $this->repository->findCompletedByAktNumber($aktNumber);

        if ($rows->isEmpty()) {
            throw new ReceivingActNotFoundException($aktNumber);
        }

        return ReceivingActData::fromRows($rows);
    }
}
