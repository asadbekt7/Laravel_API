<?php
// App/Repositories/Contracts/TelephoneRepositoryInterface.php
namespace App\Repositories\Contracts;

use App\Models\Telephonemodel;
use Illuminate\Pagination\LengthAwarePaginator;

interface TelephoneRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Telephonemodel;
    public function create(array $data): Telephonemodel;
    public function update(Telephonemodel $telephone, array $data): Telephonemodel;
    public function delete(Telephonemodel $telephone): bool;
}
