<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    private const TIMESTAMP = '2026-05-22 10:35:54';

    private const LOCATIONS = [
        ['id' => 1, 'name' => 'Asosiy ombor'],
        ['id' => 2, 'name' => 'Zaxira ombor'],
        ['id' => 3, 'name' => "Texnik bo'lim"],
    ];

    public function run(): void
    {
        foreach (self::LOCATIONS as $location) {
            DB::table('locations')->updateOrInsert(
                ['id' => $location['id']],
                [
                    'name'       => $location['name'],
                    'created_at' => self::TIMESTAMP,
                    'updated_at' => self::TIMESTAMP,
                ],
            );
        }

        $this->resetSequence();
    }

    private function resetSequence(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            "SELECT setval(pg_get_serial_sequence('locations', 'id'), COALESCE((SELECT MAX(id) FROM locations), 1))"
        );
    }
}
