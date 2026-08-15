<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BugalteriyaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batch_id,
            'batch_number' => $this->whenLoaded('batch', fn () => $this->batch?->batch_number),

            'name' => $this->name,
            'document_number' => $this->document_number,
            'supplier_name' => $this->supplier_name,

            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'model_id' => $this->model_id,

            'item_type' => $this->item_type,
            'expiry_date' => $this->expiry_date,
            'statya' => $this->statya,
            'inventory_number' => $this->inventory_number,

            'debit' => $this->debit,
            'kredit' => $this->kredit,
            'talab_qilingan' => $this->talab_qilingan,

            'full_name' => $this->full_name,
            'department' => $this->department,
            'building' => $this->building,
            'room_number' => $this->room_number,
            'room_name' => $this->room_name,

            'condition' => $this->condition,
            'notes' => $this->notes,
            'status' => $this->status,

            'items_id' => $this->items_id,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'type' => $this->whenLoaded('type'),
            'category' => $this->whenLoaded('category'),
            'model' => $this->whenLoaded('model'),
            'unit' => $this->whenLoaded('unit'),
        ];
    }
}
