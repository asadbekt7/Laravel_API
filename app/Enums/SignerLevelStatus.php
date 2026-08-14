<?php
// app/Enums/SignerLevelStatus.php
namespace App\Enums;

enum SignerLevelStatus: string
{
    case Pending  = 'pending';   // navbati kelmagan
    case Active   = 'active';    // hozir shu odamda
    case Approved = 'approved';  // tasdiqlangan (buxgalterda — "qabul qilingan")
    case Rejected = 'rejected';
}
