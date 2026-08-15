<?php
// app/Http/Resources/BugalteriyaResource.php

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
            'name' => $this->name,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'item_type' => $this->item_type,
            'full_name' => $this->full_name,
            'department' => $this->department,
            'debit' => $this->debit,
            'kredit' => $this->kredit,
            'talab_qilingan' => $this->talab_qilingan,
            'created_at' => $this->created_at,
        ];
    }
}
