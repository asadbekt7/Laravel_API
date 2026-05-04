<?php
// App/Repositories/TouchpanelRepository.php
namespace App\Repositories;

use App\Models\Touchpanelmodel;
use App\Repositories\Contracts\TouchpanelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TouchpanelRepository implements TouchpanelRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Touchpanelmodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Touchpanelmodel
    {
        return Touchpanelmodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Touchpanelmodel
    {
        return Touchpanelmodel::create($data)->load($this->with);
    }

    public function update(Touchpanelmodel $touchpanel, array $data): Touchpanelmodel
    {
        $touchpanel->update($data);
        return $touchpanel->load($this->with);
    }

    public function delete(Touchpanelmodel $touchpanel): bool
    {
        return (bool) $touchpanel->delete();
    }
}
