<?php

namespace Tests\Feature;

use App\Models\UzasboImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryImportTest extends TestCase
{
    use RefreshDatabase;

    // ─── File validation ──────────────────────────────────────────────────────

    /** @test */
    public function it_rejects_missing_file(): void
    {
        $this->postJson('/api/inventory/import', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    /** @test */
    public function it_rejects_unsupported_file_types(): void
    {
        $file = UploadedFile::fake()->create('data.pdf', 100, 'application/pdf');

        $this->postJson('/api/inventory/import', ['file' => $file])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    /** @test */
    public function it_rejects_files_over_5mb(): void
    {
        $file = UploadedFile::fake()->create('big.xlsx', 6000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->postJson('/api/inventory/import', ['file' => $file])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    // ─── Successful import ────────────────────────────────────────────────────

    /** @test */
    public function it_imports_valid_xlsx_and_returns_summary(): void
    {
        // Uses the real sample file provided by the client
        $path = base_path('tests/Fixtures/namuna.xlsx');
        $file = new UploadedFile($path, 'namuna.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->postJson('/api/inventory/import', [
            'file'        => $file,
            'import_type' => 'test_run',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'imported', 'skipped', 'errors']);

        $this->assertGreaterThan(0, $response->json('imported'));
    }

    // ─── Deduplication ────────────────────────────────────────────────────────

    /** @test */
    public function it_skips_duplicate_inventory_number_on_second_import(): void
    {
        $path = base_path('tests/Fixtures/namuna.xlsx');
        $file = fn() => new UploadedFile(
            $path, 'namuna.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null, true
        );

        // First import
        $first = $this->postJson('/api/inventory/import', ['file' => $file()]);
        $firstImported = $first->json('imported');

        // Second import — all rows should be skipped as duplicates
        $second = $this->postJson('/api/inventory/import', ['file' => $file()]);

        $second->assertJson([
            'success'  => true,
            'imported' => 0,
            'skipped'  => $firstImported,
        ]);

        $this->assertNotEmpty($second->json('errors'));
    }

    // ─── Validation errors ────────────────────────────────────────────────────

    /** @test */
    public function it_returns_errors_for_invalid_inventory_numbers(): void
    {
        // inventory_number must be exactly 14 digits — a file with short/missing ones
        // is expected to produce skipped rows and validation error messages
        $path = base_path('tests/Fixtures/invalid_inventory.xlsx');

        if (!file_exists($path)) {
            $this->markTestSkipped('Fixture file not available.');
        }

        $file = new UploadedFile($path, 'invalid_inventory.xlsx', null, null, true);

        $response = $this->postJson('/api/inventory/import', ['file' => $file]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotEmpty($response->json('errors'));
        $this->assertGreaterThan(0, $response->json('skipped'));
    }

    // ─── Response structure ───────────────────────────────────────────────────

    /** @test */
    public function response_has_correct_json_structure(): void
    {
        $path = base_path('tests/Fixtures/namuna.xlsx');
        $file = new UploadedFile($path, 'namuna.xlsx', null, null, true);

        $this->postJson('/api/inventory/import', ['file' => $file])
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'imported',
                'skipped',
                'errors',
            ]);
    }
}
