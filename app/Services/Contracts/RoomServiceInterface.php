<?php

namespace App\Services\Contracts;

interface RoomServiceInterface
{
    public function getRoomData(
        string $roomName
    ): array;
}
