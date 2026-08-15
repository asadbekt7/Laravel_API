<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateWarehouseBatchDTO;
use App\Exceptions\BatchSignException;
use App\Exceptions\InsufficientQuantityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\AcceptWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\ApproveWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\RejectWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\StoreWarehouseBatchRequest;
use App\Http\Resources\WarehouseBatchResource;
use App\Models\WarehouseBatch;
use App\Services\WarehouseBatch\WarehouseBatchAccountantService;
use App\Services\WarehouseBatch\WarehouseBatchApprovalService;
use App\Services\WarehouseBatch\WarehouseBatchService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WarehouseBatchController extends Controller
{
    /**
     * Batchlar ro'yxati — /documents oynasiga o'xshash umumiy ko'rinish.
     *
     * ?assigned_to_me=1 — faqat auth foydalanuvchining navbati "active"
     * bo'lgan batchlarni qaytaradi (buxgalter/tasdiqlovchi inbox'i sifatida ishlatiladi).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = WarehouseBatch::query()->with(['staff', 'signers.user']);

        if ($request->boolean('assigned_to_me') && $request->user()) {
            $query->whereHas('signers', fn ($q) => $q
                ->where('user_id', $request->user()->id)
                ->where('status', \App\Enums\SignerLevelStatus::Active));
        }

        $batches = $query->latest()->paginate(20);

        return WarehouseBatchResource::collection($batches);
    }

    /**
     * Bitta batchning to'liq tafsiloti — mahsulotlar va imzolovchilar bilan.
     */
    public function show(WarehouseBatch $batch): WarehouseBatchResource
    {
        if (auth()->check()) {
            $this->authorize('view', $batch);
        }

        return new WarehouseBatchResource(
            $batch->load(['items.warehouse', 'signers.user', 'staff'])
        );
    }

    /**
     * Yangi batch yaratish — ombordan mahsulot tanlanadi, xodimga biriktiriladi,
     * imzolovchilar zanjiri (1-daraja = buxgalter) shakllantiriladi.
     */
    public function store(StoreWarehouseBatchRequest $request, WarehouseBatchService $service): WarehouseBatchResource
    {
        try {
            $dto = CreateWarehouseBatchDTO::fromRequest($request->validated(), $request->user()->id);
            $batch = $service->create($dto);
        } catch (InsufficientQuantityException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    /**
     * Buxgalter — "Qabul qilish": debit/kredit/talab_qilingan to'ldiriladi,
     * so'ng navbat avtomatik keyingi tasdiqlovchiga o'tadi.
     */
    public function accept(
        AcceptWarehouseBatchRequest $request,
        WarehouseBatch $batch,
        WarehouseBatchAccountantService $service
    ): WarehouseBatchResource {
        try {
            $batch = $service->accept($batch, $request->user(), $request->validated('entries'));
        } catch (BatchSignException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    /**
     * 2-va undan keyingi darajadagi tasdiqlovchilar — oddiy tasdiqlash.
     */
    public function approve(
        ApproveWarehouseBatchRequest $request,
        WarehouseBatch $batch,
        WarehouseBatchApprovalService $service
    ): WarehouseBatchResource {
        try {
            $batch = $service->approve($batch, $request->user(), $request->validated('comment'));
        } catch (BatchSignException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    /**
     * Rad etish — zanjir shu yerda to'xtaydi, qolgan darajalar ham "rejected" bo'ladi.
     */
    public function reject(
        RejectWarehouseBatchRequest $request,
        WarehouseBatch $batch,
        WarehouseBatchApprovalService $service
    ): WarehouseBatchResource {
        try {
            $batch = $service->reject($batch, $request->user(), $request->validated('comment'));
        } catch (BatchSignException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    /**
     * Generatsiya qilingan PDF'ni xavfsiz yuklab olish.
     *
     * "local" disk temporaryUrl()ni qo'llab-quvvatlamagani uchun,
     * signed route + shu endpoint orqali fayl stream qilinadi.
     * Route "signed" middleware bilan himoyalangan (routes/api.php'ga qarang).
     */
    public function downloadPdf(WarehouseBatch $batch): StreamedResponse
    {
        if (auth()->check()) {
            $this->authorize('view', $batch);
        }

        abort_unless(
            $batch->pdf_path && Storage::disk('local')->exists($batch->pdf_path),
            404,
            'Ushbu batch uchun hali PDF generatsiya qilinmagan.'
        );

        return Storage::disk('local')->response($batch->pdf_path);
    }
}
