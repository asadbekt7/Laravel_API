<?php

namespace App\Services\Contracts;

interface EmployeeServiceInterface
{
    public function getEmployeeData(string|int $identifier): ?array;
}
