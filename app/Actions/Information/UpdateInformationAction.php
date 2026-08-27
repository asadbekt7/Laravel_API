<?php

declare(strict_types=1);

namespace App\Actions\Information;

use App\Models\InformationModel;
use App\Services\FileStorage\InformationFileUploaderInterface;
use App\Support\InformationFileFields;
use Illuminate\Http\UploadedFile;

final readonly class UpdateInformationAction
{
    public function __construct(
        private InformationFileUploaderInterface $uploader,
        private SyncInformationItemsAction $syncItemsAction,
    ) {
    }

    /**
     * @param  \App\DTO\InformationItemData[]|null  $items  null => itemlar o'zgartirilmaydi
     */
    public function execute(InformationModel $information, array $validated, ?array $items): InformationModel
    {
        $newPaths = [];
        $oldPaths = [];
        $data     = $validated;

        try {
            foreach (InformationFileFields::MAP as $prefix => $folder) {
                if (! isset($validated["{$prefix}_file"])) {
                    continue;
                }

                /** @var UploadedFile $file */
                $file   = $validated["{$prefix}_file"];
                $stored = $this->uploader->store($file, $folder);

                $newPaths[] = $stored->path;
                $oldPaths[] = $information->{"{$prefix}_file_path"};

                $data["{$prefix}_file_path"] = $stored->path;
                $data["{$prefix}_file_name"] = $stored->name;
                unset($data["{$prefix}_file"]);
            }

            $information->update($data);

            if ($items !== null) {
                $this->syncItemsAction->execute($information, $items);
            }

            // Fayllar faqat DB muvaffaqiyatli yangilangandan keyin o'chiriladi.
            foreach ($oldPaths as $oldPath) {
                $this->uploader->delete($oldPath);
            }

            return $information->fresh(['items.unit', 'supplier', 'creator']);
        } catch (\Throwable $e) {
            foreach ($newPaths as $path) {
                $this->uploader->delete($path);
            }

            throw $e;
        }
    }
}
