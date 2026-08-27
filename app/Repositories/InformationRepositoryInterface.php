<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\InformationModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InformationRepositoryInterface
{
    public function paginate(array $filters, int $perPage): LengthAwarePaginator;

    public function findOrFailWithRelations(int $id): InformationModel;
}
