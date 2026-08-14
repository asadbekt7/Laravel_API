<?php
// app/Events/BatchSignerActivated.php
namespace App\Events;

use App\Models\WarehouseBatchSigner;
use Illuminate\Foundation\Events\Dispatchable;

class BatchSignerActivated
{
    use Dispatchable;

    public function __construct(public readonly WarehouseBatchSigner $signer) {}
}
