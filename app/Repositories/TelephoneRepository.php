<?php
// App/Repositories/TelephoneRepository.php
namespace App\Repositories;

use App\Models\Telephonemodel;
use App\Repositories\Contracts\TelephoneRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TelephoneRepository implements TelephoneRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Telephonemodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Telephonemodel
    {
        return Telephonemodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Telephonemodel
    {
        return Telephonemodel::create($data)->load($this->with);
    }

    public function update(Telephonemodel $telephone, array $data): Telephonemodel
    {
        $telephone->update($data);
        return $telephone->load($this->with);
    }

    public function delete(Telephonemodel $telephone): bool
    {
        return (bool) $telephone->delete();
    }
}
