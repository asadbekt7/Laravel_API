<?php

declare(strict_types=1);

namespace App\Actions\Information;

use App\Actions\InformationItem\AddInformationItemAction;
use App\DTOs\InformationData;
use App\DTOs\InformationItemData;
use App\Models\InformationModel;
use App\Services\FileStorage\InformationFileUploaderInterface;
use App\Support\InformationFileFields;
use App\Support\StoredFile;
use Illuminate\Http\UploadedFile;

final readonly class CreateInformationAction
{
    public function __construct(
        private InformationFileUploaderInterface $uploader,
        private AddInformationItemAction $addItemAction,
    ) {
    }

    /**
     * @param  InformationItemData[]  $items
     */
    public function execute(array $validated, array $items): InformationModel
    {
        $storedPaths = [];

        try {
            $files = $this->storeFiles($validated, $storedPaths);
            $data  = $this->buildInformationData($validated, $files);

            $information = InformationModel::create($data->toArray());

            foreach ($items as $item) {
                $this->addItemAction->execute($information, $item);
            }

            return $information->load('items.unit', 'supplier', 'creator');
        } catch (\Throwable $e) {
            // Tranzaksiya rollback bo'ladi, lekin diskka yozilgan fayllar
            // avtomatik o'chmaydi - shuning uchun qo'lda tozalaymiz.
            foreach ($storedPaths as $path) {
                $this->uploader->delete($path);
            }

            throw $e;
        }
    }

    /**
     * @return array<string, StoredFile>
     */
    private function storeFiles(array $validated, array &$storedPaths): array
    {
        $result = [];

        foreach (InformationFileFields::MAP as $prefix => $folder) {
            /** @var UploadedFile $file */
            $file   = $validated["{$prefix}_file"];
            $stored = $this->uploader->store($file, $folder);

            $storedPaths[]    = $stored->path;
            $result[$prefix]  = $stored;
        }

        return $result;
    }

    private function buildInformationData(array $v, array $files): InformationData
    {
        return new InformationData(
            name: $v['name'],
            description: $v['description'] ?? null,
            contractNumber: $v['contract_number'],
            contractDate: $v['contract_date'],
            contractFile: $files['contract'],
            supplierId: (int) $v['supplier_id'],
            bildirishnomaNumber: $v['bildirishnoma_number'],
            bildirishnomaDate: $v['bildirishnoma_date'] ?? null,
            bildirishnomaFile: $files['bildirishnoma'],
            ishonchnomaNumber: $v['ishonchnoma_number'],
            ishonchnomaDate: $v['ishonchnoma_date'],
            ishonchnomaFile: $files['ishonchnoma'],
            hisobFaktura: $v['hisob_faktura'],
            hisobFakturaDate: $v['hisob_faktura_date'],
            hisobFakturaFile: $files['hisob_faktura'],
        );
    }
}
