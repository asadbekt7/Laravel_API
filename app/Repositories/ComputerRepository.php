<?php

namespace App\Repositories;

use App\Models\Computermodel;
use App\Repositories\Contracts\ComputerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ComputerRepository implements ComputerRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Computermodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Computermodel
    {
        return Computermodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Computermodel
    {
        return Computermodel::create($data)->load($this->with);
    }

    public function update(Computermodel $computer, array $data): Computermodel
    {
        $computer->update($data);
        return $computer->load($this->with);
    }

    public function delete(Computermodel $computer): bool
    {
        return (bool) $computer->delete();
    }
}
