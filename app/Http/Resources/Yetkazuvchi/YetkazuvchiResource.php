<?php

namespace App\Http\Resources\Yetkazuvchi;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YetkazuvchiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'INN_number' => $this->INN_number,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
