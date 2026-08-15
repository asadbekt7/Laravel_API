<?php
// app/Events/WarehouseBatchCompleted.php

namespace App\Events;

use App\Models\WarehouseBatch;
use Illuminate\Foundation\Events\Dispatchable;

class WarehouseBatchCompleted
{
    use Dispatchable;

    public function __construct(public readonly WarehouseBatch $batch) {}
}
