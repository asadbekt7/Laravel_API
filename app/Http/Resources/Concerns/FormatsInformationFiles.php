<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use App\Models\InformationModel;
use Illuminate\Support\Facades\Storage;

trait FormatsInformationFiles
{
    /**
     * information jadvalidagi 4 xil hujjat faylini (shartnoma, bildirishnoma,
     * hisob-faktura, ishonchnoma) bir xil formatda qaytaradi.
     */
    protected function informationFiles(?InformationModel $information): ?array
    {
        if (! $information) {
            return null;
        }

        return [
            'contract'      => $this->fileEntry($information->contract_file_name, $information->contract_file_path),
            'bildirishnoma' => $this->fileEntry($information->bildirishnoma_file_name, $information->bildirishnoma_file_path),
            'hisob_faktura' => $this->fileEntry($information->hisob_faktura_file_name, $information->hisob_faktura_file_path),
            'ishonchnoma'   => $this->fileEntry($information->ishonchnoma_file_name, $information->ishonchnoma_file_path),
        ];
    }

    private function fileEntry(?string $name, ?string $path): ?array
    {
        if (! $path) {
            return null;
        }

        return [
            'name' => $name,
            'url'  => Storage::url($path),
        ];
    }
}
