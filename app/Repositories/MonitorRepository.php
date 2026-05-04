<?php
// App/Repositories/MonitorRepository.php
namespace App\Repositories;

use App\Models\Monitormodel;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MonitorRepository implements MonitorRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Monitormodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Monitormodel
    {
        return Monitormodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Monitormodel
    {
        return Monitormodel::create($data)->load($this->with);
    }

    public function update(Monitormodel $monitor, array $data): Monitormodel
    {
        $monitor->update($data);
        return $monitor->load($this->with);
    }

    public function delete(Monitormodel $monitor): bool
    {
        return (bool) $monitor->delete();
    }
}
