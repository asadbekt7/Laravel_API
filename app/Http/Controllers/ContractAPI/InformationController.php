<?php

namespace App\Http\Controllers\ContractAPI;

use App\Enums\InformationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Information\BulkInformationRequest;
use App\Http\Requests\Information\StoreInformationRequest;
use App\Http\Resources\Information\InformationResource;
use App\Models\InformationModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    /**
     * Hujjat prefikslari va saqlanadigan papkalar.
     * Request'da maydon: {prefix}_file (masalan contract_file)
     * Bazada ustunlar:   {prefix}_file_path, {prefix}_file_name
     */
    private const FILE_FIELDS = [
        'contract'      => 'contracts',
        'bildirishnoma' => 'bildirishnomalar',
        'ishonchnoma'   => 'ishonchnomalar',
        'hisob_faktura' => 'hisob_fakturalar',
    ];

    /* ----------------------------------------------------------------
     * Yordamchi metodlar
     * ---------------------------------------------------------------- */

    /**
     * Request'dagi fayllarni saqlab, $data ga path/name qo'shadi.
     * Saqlangan path'lar $storedPaths ga yig'iladi — xato bo'lsa
     * ularni tozalash uchun.
     */
    private function storeFiles(Request $request, array $data, array &$storedPaths): array
    {
        foreach (self::FILE_FIELDS as $prefix => $folder) {
            if (! $request->hasFile("{$prefix}_file")) {
                continue;
            }

            $file = $request->file("{$prefix}_file");
            $path = $file->store("informations/{$folder}", 'public');

            $storedPaths[] = $path;

            $data["{$prefix}_file_path"] = $path;
            $data["{$prefix}_file_name"] = $file->getClientOriginalName();
        }

        // Fayl maydonlarining o'zi bazaga yozilmasligi kerak
        foreach (array_keys(self::FILE_FIELDS) as $prefix) {
            unset($data["{$prefix}_file"]);
        }

        return $data;
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /* ================================================================
     * INDEX — Ro'yxat (filter + qidiruv + pagination)
     * GET /api/information
     * ================================================================ */
    public function index(Request $request): AnonymousResourceCollection
    {
        $informations = InformationModel::with('supplier', 'unit', 'creator', 'assignee')
            // Qidiruv shartlari closure ichida — boshqa filtrlarni buzmaydi
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('contract_number', 'ilike', "%{$search}%")
                    ->orWhere('product_name', 'ilike', "%{$search}%");
            }))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->from_date, fn ($q, $date) => $q->whereDate('contract_date', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->whereDate('contract_date', '<=', $date))
            ->when($request->status, function ($q, $status) {
                if ($enum = InformationStatus::tryFrom($status)) {
                    $q->where('status', $enum);
                }
            })
            ->when($request->boolean('assigned_to_me'), fn ($q) => $q->assignedTo(auth()->id()))
            ->when($request->boolean('created_by_me'), fn ($q) => $q->where('creator_id', auth()->id()))
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return InformationResource::collection($informations);
    }

    /* ================================================================
     * PENDING — Qabul qilinishi kutilayotganlar
     * GET /api/information/pending
     * ================================================================ */
    public function pending(Request $request): AnonymousResourceCollection
    {
        $informations = InformationModel::with('supplier', 'unit', 'creator')
            ->pending()
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return InformationResource::collection($informations);
    }

    /* ================================================================
     * MY TASKS — Menga biriktirilgan ishlar
     * GET /api/information/my-tasks
     * ================================================================ */
    public function myTasks(Request $request): AnonymousResourceCollection
    {
        $informations = InformationModel::with('supplier', 'unit', 'creator')
            ->assignedTo(auth()->id())
            ->when($request->status, function ($q, $status) {
                if ($enum = InformationStatus::tryFrom($status)) {
                    $q->where('status', $enum);
                }
            })
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return InformationResource::collection($informations);
    }

    /* ================================================================
     * SHOW — Bitta yozuv
     * GET /api/information/{information}
     * ================================================================ */
    public function show(InformationModel $information): InformationResource
    {
        return new InformationResource(
            $information->load('supplier', 'unit', 'creator', 'assignee')
        );
    }

    /* ================================================================
     * STORE — Yaratish
     * POST /api/information
     * (creator_id va status=Pending model booted() ichida avtomatik)
     * ================================================================ */
    public function store(StoreInformationRequest $request): JsonResponse
    {
        $storedPaths = [];

        try {
            // Fayllar transaction'dan TASHQARIDA saqlanadi —
            // rollback bo'lsa ularni o'zimiz tozalaymiz
            $data = $this->storeFiles($request, $request->validated(), $storedPaths);

            $information = DB::transaction(
                fn () => InformationModel::create($data)
            );

            return response()->json([
                'message' => 'Muvaffaqiyatli saqlandi.',
                'data'    => new InformationResource(
                    $information->load('supplier', 'unit', 'creator')
                ),
            ], 201);

        } catch (\Throwable $e) {
            $this->deleteFiles($storedPaths);
            Log::error('Information store xatosi', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Saqlashda xatolik yuz berdi.',
            ], 500);
        }
    }

    /* ================================================================
     * BULK STORE — Bitta hujjat to'plami, bir nechta mahsulot
     * POST /api/information/bulk
     * ================================================================ */
    public function bulkStore(BulkInformationRequest $request): JsonResponse
    {
        $storedPaths = [];

        try {
            $data = $this->storeFiles($request, $request->validated(), $storedPaths);

            $items = $data['items'];
            unset($data['items']);

            /** @var EloquentCollection $created */
            $created = DB::transaction(function () use ($items, $data) {
                $created = new EloquentCollection();

                foreach ($items as $item) {
                    $created->push(InformationModel::create(array_merge($data, $item)));
                }

                return $created;
            });

            return response()->json([
                'message' => $created->count() . ' ta mahsulot muvaffaqiyatli saqlandi.',
                'data'    => InformationResource::collection(
                    $created->load('supplier', 'unit', 'creator')
                ),
            ], 201);

        } catch (\Throwable $e) {
            $this->deleteFiles($storedPaths);
            Log::error('Information bulk store xatosi', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Saqlashda xatolik yuz berdi.',
            ], 500);
        }
    }

    /* ================================================================
     * STATUS BOSHQARUVI — hammasi POST, model metodlariga tayanadi
     * ================================================================ */

    /**
     * POST /api/information/{information}/accept
     */
    public function accept(InformationModel $information): JsonResponse
    {
        if (! $information->accept()) {
            return response()->json([
                'message' => 'Bu ma\'lumotni qabul qilib bo\'lmaydi. U allaqachon qabul qilingan yoki yakunlangan.',
            ], 422);
        }

        return response()->json([
            'message' => 'Muvaffaqiyatli qabul qilindi.',
            'data'    => new InformationResource(
                $information->load('supplier', 'unit', 'creator', 'assignee')
            ),
        ]);
    }

    /**
     * POST /api/information/{information}/start
     */
    public function start(InformationModel $information): JsonResponse
    {
        // Aniqroq xabar uchun: status to'g'ri, lekin boshqa odam bosayotgan bo'lsa — 403
        if ($information->status === InformationStatus::Accepted
            && auth()->id() !== $information->assignee_id) {
            return response()->json([
                'message' => 'Faqat qabul qilgan foydalanuvchi ishni boshlashi mumkin.',
            ], 403);
        }

        if (! $information->startProgress()) {
            return response()->json([
                'message' => 'Ishni boshlab bo\'lmaydi. Avval ma\'lumot qabul qilingan bo\'lishi kerak.',
            ], 422);
        }

        return response()->json([
            'message' => 'Ish boshlandi.',
            'data'    => new InformationResource(
                $information->load('supplier', 'unit', 'creator', 'assignee')
            ),
        ]);
    }

    /**
     * POST /api/information/{information}/complete
     */
    public function complete(InformationModel $information): JsonResponse
    {
        if (in_array($information->status, [InformationStatus::Accepted, InformationStatus::InProgress], true)
            && auth()->id() !== $information->assignee_id) {
            return response()->json([
                'message' => 'Faqat qabul qilgan foydalanuvchi ishni yakunlashi mumkin.',
            ], 403);
        }

        if (! $information->complete()) {
            return response()->json([
                'message' => 'Ishni yakunlab bo\'lmaydi. Ma\'lumot qabul qilingan yoki jarayonda bo\'lishi kerak.',
            ], 422);
        }

        return response()->json([
            'message' => 'Ish muvaffaqiyatli yakunlandi.',
            'data'    => new InformationResource(
                $information->load('supplier', 'unit', 'creator', 'assignee')
            ),
        ]);
    }
}
