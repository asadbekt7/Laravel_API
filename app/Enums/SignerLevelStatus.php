<?php
// app/Enums/SignerLevelStatus.php

namespace App\Enums;

enum SignerLevelStatus: string
{
    case Pending  = 'pending';
    case Active   = 'active';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
