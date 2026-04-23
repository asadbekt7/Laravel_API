<?php

namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Services\Contracts\RoomServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoomApiService implements RoomServiceInterface
{
    private string $baseUrl = 'https://manage.uwed.uz/api/timetable/public/v1/search/rooms';
    private int $timeout = 10;

    public function getRoomData(string $roomName): array
    {
        $response = $this->sendRequest($roomName);
        $rooms    = $this->parseResponse($response);
        return $this->findMatchingRoom($rooms, $roomName);
    }

    private function sendRequest(string $roomName): \Illuminate\Http\Client\Response
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl, ['search' => $roomName]);

            if ($response->failed()) {
                Log::error('Room API HTTP xatosi', [
                    'room_name'   => $roomName,
                    'status_code' => $response->status(),
                ]);
                throw RoomApiException::requestFailed();
            }

            return $response;

        } catch (RoomApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Room API ga ulanib bo\'lmadi', [
                'room_name' => $roomName,
                'error'     => $e->getMessage(),
            ]);
            throw RoomApiException::requestFailed($e);
        }
    }

    private function parseResponse(\Illuminate\Http\Client\Response $response): array
    {
        $json = $response->json();

        if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        if (is_array($json) && array_is_list($json)) {
            return $json;
        }

        Log::error('Room API kutilmagan javob strukturasi', ['response' => $json]);
        throw RoomApiException::invalidResponse();
    }

    private function findMatchingRoom(array $rooms, string $roomName): array
    {
        foreach ($rooms as $room) {
            $apiRoomName = $room['name'] ?? $room['room_name'] ?? null;

            if ($apiRoomName === null) {
                continue;
            }

            if (mb_strtolower(trim($apiRoomName)) === mb_strtolower(trim($roomName))) {
                return [
                    'room_name'   => $apiRoomName,
                    'room_number' => (string) ($room['room_number'] ?? $room['number'] ?? ''),
                    'building'    => (string) ($room['building']    ?? $room['corpus']  ?? ''),
                ];
            }
        }

        Log::warning('Room API da xona topilmadi', ['searched' => $roomName]);
        throw RoomApiException::roomNotFound($roomName);
    }
}
