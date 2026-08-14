<?php
// app/Enums/BatchStatus.php
namespace App\Enums;

enum BatchStatus: string
{
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Rejected   = 'rejected';
}
