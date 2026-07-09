<?php

namespace App\Http\Resources\Information;

use App\Enums\InformationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,

            // Asosiy ma'lumotlar
            'name'        => $this->name,
            'description' => $this->description,

            // Shartnoma
            'contract_number'    => $this->contract_number,
            'contract_date'      => $this->contract_date?->format('Y-m-d'),
            'contract_file_url'  => $this->fileUrl($this->contract_file_path),
            'contract_file_name' => $this->contract_file_name,

            // Yetkazib beruvchi
            'supplier_id' => $this->supplier_id,
            'supplier'    => $this->whenLoaded('supplier', fn () => [
                'id'   => $this->supplier->id,
                'name' => $this->supplier->name,
            ]),

            // Bildirishnoma
            'bildirishnoma_number'    => $this->bildirishnoma_number,
            'bildirishnoma_date'      => $this->bildirishnoma_date?->format('Y-m-d'),
            'bildirishnoma_file_url'  => $this->fileUrl($this->bildirishnoma_file_path),
            'bildirishnoma_file_name' => $this->bildirishnoma_file_name,

            // Mahsulot
            'product_name' => $this->product_name,
            'quantity'     => $this->quantity,
            'price'        => $this->price !== null ? (string) $this->price : null,

            // O'lchov birligi
            'unit_id' => $this->unit_id,
            'unit'    => $this->whenLoaded('unit', fn () => [
                'id'   => $this->unit->id,
                'name' => $this->unit->name,
            ]),

            // Ishonchnoma
            'ishonchnoma_number'    => $this->ishonchnoma_number,
            'ishonchnoma_date'      => $this->ishonchnoma_date?->format('Y-m-d'),
            'ishonchnoma_file_url'  => $this->fileUrl($this->ishonchnoma_file_path),
            'ishonchnoma_file_name' => $this->ishonchnoma_file_name,

            // Hisob-faktura
            'hisob_faktura'           => $this->hisob_faktura,
            'hisob_faktura_date'      => $this->hisob_faktura_date?->format('Y-m-d'),
            'hisob_faktura_file_url'  => $this->fileUrl($this->hisob_faktura_file_path),
            'hisob_faktura_file_name' => $this->hisob_faktura_file_name,

            // Akt
            'akt_number' => $this->akt_number,
            'akt_date'   => $this->akt_date?->format('Y-m-d'),

            // Status / workflow
            'status'       => $this->status?->value,
            'accepted_at'  => $this->accepted_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),

            // Kim yaratgan / kim qabul qilgan
            'creator' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ] : null),
            'assignee' => $this->whenLoaded('assignee', fn () => $this->assignee ? [
                'id'   => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null),

            // Frontend uchun: joriy user qaysi amallarni bajara oladi
            'can' => $this->permissions($request),

            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Fayl path'ini to'liq URL'ga aylantiradi.
     */
    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    /**
     * Joriy foydalanuvchi uchun ruxsat etilgan amallar.
     */
    private function permissions(Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'accept'   => $this->status === InformationStatus::Pending,

            'start'    => $this->status === InformationStatus::Accepted
                && $this->assignee_id === $userId,

            'complete' => in_array($this->status, [
                    InformationStatus::Accepted,
                    InformationStatus::InProgress,
                ], true)
                && $this->assignee_id === $userId,
        ];
    }
}
