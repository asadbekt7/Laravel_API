<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'uzasbo_import_id' => $this->uzasbo_import_id,
            'name'             => $this->name,
            'inventory_number' => $this->inventory_number,
            'type_id'          => $this->type_id,
            'category_id'      => $this->category_id,
            'model_id'         => $this->model_id,
            'unit_id'          => $this->unit_id,
            'quantity'         => $this->quantity,
            'room_name'        => $this->room_name,
            'building'         => $this->building,
            'room_number'      => $this->room_number,
            'lastName'         => $this->last_name,
            'firstName'        => $this->first_name,
            'middleName'       => $this->middle_name,
            'condition'        => $this->condition,
            'status'           => $this->status,
            'notes'            => $this->notes,
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
