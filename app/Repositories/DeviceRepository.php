<?php
// App/Repositories/DeviceRepository.php
namespace App\Repositories;

use App\Models\Devicemodel;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceRepository implements DeviceRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Devicemodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Devicemodel
    {
        return Devicemodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Devicemodel
    {
        return Devicemodel::create($data)->load($this->with);
    }

    public function update(Devicemodel $device, array $data): Devicemodel
    {
        $device->update($data);
        return $device->load($this->with);
    }

    public function delete(Devicemodel $device): bool
    {
        return (bool) $device->delete();
    }
}
