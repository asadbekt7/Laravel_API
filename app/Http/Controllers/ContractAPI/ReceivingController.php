<?php

declare(strict_types=1);

namespace App\Http\Controllers\ContractAPI;

use App\Actions\Receiving\GenerateReceivingActPdfAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receiving\ReceivingIndexRequest;
use App\Http\Resources\Receiving\ReceivingActDetailResource;
use App\Http\Resources\Receiving\ReceivingActListResource;
use App\Services\Receiving\ReceivingService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReceivingController extends Controller
{
    public function __construct(
        private readonly ReceivingService $service,
    ) {
    }

    /**
     * GET /api/receiving
     * Yakunlangan (status=completed) barcha aktlarni akt_number bo'yicha
     * guruhlab, sahifalab qaytaradi.
     */
    public function index(ReceivingIndexRequest $request): AnonymousResourceCollection
    {
        return ReceivingActListResource::collection($this->service->list($request));
    }

    /**
     * GET /api/receiving/{aktNumber}
     * Berilgan akt_number'ga tegishli BARCHA mahsulot qatorlarini
     * (header + items) bitta obyektda qaytaradi.
     */
    public function show(string $aktNumber): ReceivingActDetailResource
    {
        return new ReceivingActDetailResource($this->service->show($aktNumber));
    }

    /**
     * GET /api/receiving/{aktNumber}/pdf
     * Xuddi shu ma'lumotni "Приёмный акт" blankiga mos PDF ko'rinishida beradi.
     * ?download=1 — brauzerda ko'rsatish o'rniga faylni yuklab olishga majburlaydi.
     */
    public function pdf(
        string $aktNumber,
        Request $request,
        GenerateReceivingActPdfAction $action,
    ): Responsable {
        $act = $this->service->show($aktNumber);

        return $action->execute($act, $request->boolean('download'), (string) $request->query('lang', 'ru'));
    }
}
