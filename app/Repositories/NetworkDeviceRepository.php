<?php
// App/Repositories/NetworkDeviceRepository.php
namespace App\Repositories;

use App\Models\NetworkDevicemodel;
use App\Repositories\Contracts\NetworkDeviceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NetworkDeviceRepository implements NetworkDeviceRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return NetworkDevicemodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): NetworkDevicemodel
    {
        return NetworkDevicemodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): NetworkDevicemodel
    {
        return NetworkDevicemodel::create($data)->load($this->with);
    }

    public function update(NetworkDevicemodel $networkDevice, array $data): NetworkDevicemodel
    {
        $networkDevice->update($data);
        return $networkDevice->load($this->with);
    }

    public function delete(NetworkDevicemodel $networkDevice): bool
    {
        return (bool) $networkDevice->delete();
    }
}
