<?php

namespace App\Repositories\Contracts;

use App\Models\Computermodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface ComputerRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Computermodel;
    public function create(array $data): Computermodel;
    public function update(Computermodel $computer, array $data): Computermodel;
    public function delete(Computermodel $computer): bool;
}
