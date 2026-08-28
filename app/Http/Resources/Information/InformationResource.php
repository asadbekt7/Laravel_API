<?php

declare(strict_types=1);

namespace App\Http\Resources\Information;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'reject_reason' => $this->reject_reason,

            'contract' => [
                'number' => $this->contract_number,
                'date'   => $this->contract_date?->toDateString(),
                'file'   => $this->fileInfo($this->contract_file_path, $this->contract_file_name),
            ],
            'bildirishnoma' => [
                'number' => $this->bildirishnoma_number,
                'date'   => $this->bildirishnoma_date?->toDateString(),
                'file'   => $this->fileInfo($this->bildirishnoma_file_path, $this->bildirishnoma_file_name),
            ],
            'ishonchnoma' => [
                'number' => $this->ishonchnoma_number,
                'date'   => $this->ishonchnoma_date?->toDateString(),
                'file'   => $this->fileInfo($this->ishonchnoma_file_path, $this->ishonchnoma_file_name),
            ],
            'hisob_faktura' => [
                'number' => $this->hisob_faktura,
                'date'   => $this->hisob_faktura_date?->toDateString(),
                'file'   => $this->fileInfo($this->hisob_faktura_file_path, $this->hisob_faktura_file_name),
            ],

            'supplier' => $this->whenLoaded('supplier'),
            'creator'  => $this->whenLoaded('creator'),

            'items'       => InformationItemResource::collection($this->whenLoaded('items')),
            'items_total' => $this->whenLoaded('items', fn () => (float) $this->items->sum('total_price')),

            'accepted_at'  => $this->accepted_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }

    private function fileInfo(?string $path, ?string $name): ?array
    {
        if (! $path) {
            return null;
        }

        return [
            'name' => $name,
            'url'  => Storage::disk('public')->url($path),
        ];
    }
}
