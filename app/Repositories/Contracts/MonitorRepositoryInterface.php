<?php
// App/Repositories/Contracts/MonitorRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Monitormodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface MonitorRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Monitormodel;
    public function create(array $data): Monitormodel;
    public function update(Monitormodel $monitor, array $data): Monitormodel;
    public function delete(Monitormodel $monitor): bool;
}
