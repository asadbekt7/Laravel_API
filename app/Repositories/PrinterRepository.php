<?php
// App/Repositories/PrinterRepository.php
namespace App\Repositories;

use App\Models\Printermodel;
use App\Repositories\Contracts\PrinterRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PrinterRepository implements PrinterRepositoryInterface
{
    private array $with = ['category', 'model', 'unit', 'inventorynumber'];

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Printermodel::with($this->with)->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Printermodel
    {
        return Printermodel::with($this->with)->findOrFail($id);
    }

    public function create(array $data): Printermodel
    {
        return Printermodel::create($data)->load($this->with);
    }

    public function update(Printermodel $printer, array $data): Printermodel
    {
        $printer->update($data);
        return $printer->load($this->with);
    }

    public function delete(Printermodel $printer): bool
    {
        return (bool) $printer->delete();
    }
}
