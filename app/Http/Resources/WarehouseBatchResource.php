<?php
// app/Http/Resources/WarehouseBatchResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_number' => $this->batch_number,
            'status' => $this->status,
            'staff' => $this->whenLoaded('staff', fn () => ['id' => $this->staff->id, 'name' => $this->staff->name]),
            'pdf_url' => $this->pdf_path ? \Storage::disk('local')->temporaryUrl($this->pdf_path, now()->addMinutes(10)) : null,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($i) => [
                'id' => $i->id,
                'warehouse_name' => $i->warehouse->name,
                'quantity' => $i->quantity,
                'talab_qilingan' => $i->talab_qilingan,
                'debit' => $i->debit,
                'kredit' => $i->kredit,
            ])),
            'signers' => $this->whenLoaded('signers', fn () => $this->signers->map(fn ($s) => [
                'level' => $s->level,
                'name' => $s->user->name,
                'status' => $s->status,
                'responded_at' => $s->responded_at,
                'comment' => $s->comment,
            ])),
            'created_at' => $this->created_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
