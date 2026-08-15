<?php

namespace App\Http\Resources;

use App\Enums\SignerLevelStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class WarehouseBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeSigner = $this->relationLoaded('signers')
            ? $this->signers->firstWhere('status', SignerLevelStatus::Active)
            : null;

        $userId = $request->user()?->id;

        $firstEntry = $this->relationLoaded('entries') ? $this->entries->first() : null;

        return [
            'id' => $this->id,
            'batch_number' => $this->batch_number,
            'status' => $this->status,
            'staff' => $firstEntry ? [
                'full_name' => $firstEntry->full_name,
                'department' => $firstEntry->department,
            ] : null,
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->full_name ?? $this->createdBy->name,
            ]),
            'active_level' => $activeSigner?->level,
            'can_act' => (bool) ($activeSigner && $userId && $activeSigner->user_id === $userId),
            'pdf_url' => ($this->file_path && Route::has('warehouse-batches.pdf'))
                ? URL::temporarySignedRoute('warehouse-batches.pdf', now()->addMinutes(10), ['batch' => $this->id])
                : null,
            'entries' => $this->whenLoaded('entries', fn () => $this->entries->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'quantity' => $e->quantity,
                'status' => $e->status,
                'debit' => $e->debit,
                'kredit' => $e->kredit,
                'talab_qilingan' => $e->talab_qilingan,
            ])),
            'signers' => $this->whenLoaded('signers', fn () => $this->signers->map(fn ($s) => [
                'level' => $s->level,
                'name' => $s->user?->full_name ?? $s->user?->name,
                'status' => $s->status,
                'responded_at' => $s->responded_at,
                'comment' => $s->comment,
            ])),
            'created_at' => $this->created_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
