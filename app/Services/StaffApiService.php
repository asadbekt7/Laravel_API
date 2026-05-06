<?php

namespace App\Services;

use App\Services\ExternalApi\AbstractApiService;

class StaffApiService extends AbstractApiService
{
    private const ENDPOINT_STAFF = 'all/staff';

    protected function apiName(): string
    {
        return 'StaffAPI';
    }

    protected function baseUrl(): string
    {
        return rtrim(config('services.staff_api.base_url'), '/');
    }

    protected function defaultHeaders(): array
    {
        $headers = parent::defaultHeaders();

        $token = config('services.staff_api.api_key');
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    /**
     * GET /staff  →  Return all staff members.
     *
     * @return array<mixed>
     */
    public function getAllStaff(): array
    {
        return $this->getList(self::ENDPOINT_STAFF);
    }

    /**
     * GET /staff?department=X  →  Return staff filtered by department.
     *
     * @return array<mixed>
     */
    public function getStaffByDepartment(string $department): array
    {
        return $this->getList(self::ENDPOINT_STAFF, ['department' => $department]);
    }

    /**
     * GET /staff/{id}  →  Return a single staff member.
     *
     * @return array<mixed>
     */
    public function getStaffById(int|string $id): array
    {
        return $this->getById(self::ENDPOINT_STAFF, $id);
    }
}
