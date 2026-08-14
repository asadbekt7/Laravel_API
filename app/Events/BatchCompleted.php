<?php
// app/Events/BatchCompleted.php
namespace App\Events;

use App\Models\WarehouseBatch;
use Illuminate\Foundation\Events\Dispatchable;

class BatchCompleted
{
    use Dispatchable;

    public function __construct(public readonly WarehouseBatch $batch) {}
}
