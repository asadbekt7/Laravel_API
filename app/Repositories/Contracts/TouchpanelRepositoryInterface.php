<?php
// App/Repositories/Contracts/TouchpanelRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Touchpanelmodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface TouchpanelRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Touchpanelmodel;
    public function create(array $data): Touchpanelmodel;
    public function update(Touchpanelmodel $touchpanel, array $data): Touchpanelmodel;
    public function delete(Touchpanelmodel $touchpanel): bool;
}
