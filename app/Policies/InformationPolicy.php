<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\InformationStatus;
use App\Models\InformationModel;
use App\Models\User;

class InformationPolicy
{
    /**
     * Faqat yaratgan foydalanuvchi VA faqat quyidagi holatlarda tahrirlay oladi:
     *  - Pending  -> hali ombor ko'rib chiqmagan.
     *  - Rejected -> ombor rad etgan, xatoni to'g'irlab qayta yuborish uchun.
     */
    public function update(User $user, InformationModel $information): bool
    {
        return $information->creator_id === $user->id
            && in_array($information->status, [InformationStatus::Pending, InformationStatus::Rejected], true);
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
