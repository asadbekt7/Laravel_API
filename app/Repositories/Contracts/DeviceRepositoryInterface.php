<?php
// App/Repositories/Contracts/DeviceRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Devicemodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeviceRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Devicemodel;
    public function create(array $data): Devicemodel;
    public function update(Devicemodel $device, array $data): Devicemodel;
    public function delete(Devicemodel $device): bool;
}
