<?php
// app/Policies/WarehouseBatchPolicy.php

namespace App\Policies;

use App\Enums\SignerLevelStatus;
use App\Models\User;
use App\Models\WarehouseBatch;

class WarehouseBatchPolicy
{
    public function act(User $user, WarehouseBatch $batch): bool
    {
        return $batch->signers()
            ->where('user_id', $user->id)
            ->where('status', SignerLevelStatus::Active)
            ->exists();
    }

    public function view(User $user, WarehouseBatch $batch): bool
    {
        return $batch->created_by === $user->id
            || $batch->signers()->where('user_id', $user->id)->exists();
    }
}
