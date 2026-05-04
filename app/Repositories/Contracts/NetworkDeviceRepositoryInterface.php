<?php
// App/Repositories/Contracts/NetworkDeviceRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\NetworkDevicemodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface NetworkDeviceRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): NetworkDevicemodel;
    public function create(array $data): NetworkDevicemodel;
    public function update(NetworkDevicemodel $networkDevice, array $data): NetworkDevicemodel;
    public function delete(NetworkDevicemodel $networkDevice): bool;
}
