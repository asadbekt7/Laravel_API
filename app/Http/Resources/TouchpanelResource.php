<?php
// App/Http/Resources/TouchpanelResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TouchpanelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'quantity' => $this->quantity,

            'room' => [
                'room_name'   => $this->room_name,
                'building'    => $this->building,
                'room_number' => $this->room_number,
            ],

            'inventory' => [
                'id'               => $this->inventory_number_id,
                'inventory_number' => $this->whenLoaded('inventoryNumber',
                    fn () => $this->inventoryNumber->inventory_number
                ),
            ],

            'employee' => [
                'id'   => $this->employee_id,
                'name' => $this->employee_name,
            ],

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
