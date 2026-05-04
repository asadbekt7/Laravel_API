<?php
// App/Repositories/Contracts/PrinterRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Printermodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface PrinterRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Printermodel;
    public function create(array $data): Printermodel;
    public function update(Printermodel $printer, array $data): Printermodel;
    public function delete(Printermodel $printer): bool;
}
