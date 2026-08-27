<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\InformationStatus;
use App\Models\InformationModel;
use App\Models\User;

class InformationPolicy
{
    /**
     * Faqat yaratgan foydalanuvchi VA faqat "pending" holatda tahrirlay oladi.
     * (accepted/in_progress/completed bosqichida header/itemlar o'zgarmasligi
     * kerak — bu biznes qoidasi eski kodda ham bor edi, saqlab qoldim.)
     */
    public function update(User $user, InformationModel $information): bool
    {
        return $information->creator_id === $user->id
            && $information->status === InformationStatus::Pending;
    }

    public function delete(User $user, InformationModel $information): bool
    {
        return $this->update($user, $information);
    }

    public function manageItems(User $user, InformationModel $information): bool
    {
        return $this->update($user, $information);
    }
}
