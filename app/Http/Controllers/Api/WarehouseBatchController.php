<?php
// app/Http/Controllers/Api/WarehouseBatchController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\ApproveWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\RejectWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\SignAsAccountantRequest;
use App\Http\Requests\WarehouseBatch\StoreWarehouseBatchRequest;
use App\Http\Resources\WarehouseBatchResource;
use App\Models\WarehouseBatch;
use App\Services\PdfService;
use App\Services\WarehouseBatch\WarehouseBatchApprovalService;
use App\Services\WarehouseBatch\WarehouseBatchCreationService;
use App\Services\WarehouseBatch\WarehouseBatchSignService;
use App\Enums\SignerLevelStatus;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WarehouseBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseBatch::with(['entries', 'signers.user', 'createdBy'])
            ->latest();

        if ($request->boolean('assigned_to_me') && $request->user()) {
            $userId = $request->user()->id;
            $query->whereHas('signers', fn ($q) => $q
                ->where('user_id', $userId)
                ->where('status', SignerLevelStatus::Active));
        }

        return WarehouseBatchResource::collection(
            $query->paginate((int) $request->input('per_page', 20))
        );
    }

    public function store(StoreWarehouseBatchRequest $request, WarehouseBatchCreationService $service)
    {
        try {
            $batch = $service->create(
                batchNumber: $request->validated('batch_number'),
                createdBy: $request->user()->id,
                signers: $request->validated('signers'),
            );
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                abort(422, 'Bu batch raqami allaqachon mavjud.');
            }

            abort(500, 'Partiya yaratishda xatolik: '.$e->getMessage());
        }

        return new WarehouseBatchResource($batch->load('createdBy'));
    }

    public function show(WarehouseBatch $batch): WarehouseBatchResource
    {
        $this->authorize('view', $batch);

        return new WarehouseBatchResource(
            $batch->load(['entries.warehouse', 'signers.user', 'createdBy'])
        );
    }

    public function items(WarehouseBatch $batch)
    {
        $this->authorize('view', $batch);

        return response()->json(['data' => $batch->items()]);
    }

    public function pdf(Request $request, WarehouseBatch $batch, PdfService $pdf)
    {
        $this->authorize('view', $batch);

        $fileName = "yuk-xati-{$batch->id}.pdf";
        $lang = $request->query('lang');

        if (in_array($lang, ['uz', 'ru', 'en'], true)) {
            $batch->load(['entries.warehouse.unit', 'signers.user', 'createdBy']);

            $document = $pdf->fromView('yuk-xati', ['batch' => $batch, 'lang' => $lang], $fileName);

            return $request->boolean('download') ? $document->download($fileName) : $document;
        }

        abort_unless($batch->file_path && Storage::disk('local')->exists($batch->file_path), 404);

        return Storage::disk('local')->response($batch->file_path);
    }

    public function signAsAccountant(SignAsAccountantRequest $request, WarehouseBatch $batch, WarehouseBatchSignService $service)
    {
        try {
            $batch = $service->signAsAccountant($batch, $request->user(), $request->validated('entries'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    public function approve(ApproveWarehouseBatchRequest $request, WarehouseBatch $batch, WarehouseBatchApprovalService $service)
    {
        try {
            $batch = $service->approve($batch, $request->user(), $request->validated('comment'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    public function reject(RejectWarehouseBatchRequest $request, WarehouseBatch $batch, WarehouseBatchApprovalService $service)
    {
        try {
            $batch = $service->reject($batch, $request->user(), $request->validated('comment'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }
}
