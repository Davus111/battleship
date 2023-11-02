<?php

namespace App\Http\Services;

use App\Exceptions\NotAllShipsSetException;
use App\Exceptions\UserNotAuthorizedException;
use App\Models\PlayerBattleship;
use App\Models\PlayerShot;
use App\Models\Room;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RoomService
{

    public function __construct(private BattleshipService $battleshipService)
    {
    }

    public function createRoom(User $user): string
    {
        if ($this->userOwnsRoom($user->id)) throw new Exception('You already own a room', 403);

        Room::create([
            'owner_id' => $user->id,
        ]);
        return 'Room created successfully';
    }

    public function join(int $userId, Room $room): string
    {
        if ($this->userOwnsRoom($userId)) throw new Exception('You already own a room', 403);
        if (!$room->is_open) throw new Exception('This room is full', 403);

        DB::beginTransaction();
        $this->isUserInRoom($userId);
        $room->update([
            'player_id' => $userId,
            'is_open' => 0,
        ]);
        DB::commit();
        return 'Room joined successfully';
    }

    public function leave(int $userId, $room): string
    {
            DB::beginTransaction();
            if ($this->userOwnsRoom($userId)) {
                $room->delete();
                DB::commit();

                return 'You left and removed the room succesfully';
            } else {
                $this->userLeaveRoom($room);
                DB::commit();
                return 'You left the room';
            }
    }

    public function startRoom(Room $room, int $userId): string
    {
        DB::beginTransaction();
        $this->checkOwner($room->owner_id, $userId);
        if ($this->battleshipService->getPlayerAliveBattleshipsAmount($room->id, $room->owner_id) !== 7 && $this->battleshipService->getPlayerAliveBattleshipsAmount($room->id, $room->player_id) !== 7 && $room->player_turn !== null) {
            throw new NotAllShipsSetException;
        };

        $room->update(['player_turn' => $room->owner_id]);
        DB::commit();
        return "Game has started, it is your turn";
    }

    public function restartRoom(Room $room, int $userId): string
    {
        DB::beginTransaction();
        $this->checkOwner($room->owner_id, $userId);
        $room->update(['player_turn' => null]);

        PlayerBattleship::where('room_id', $room->id)->delete();
        PlayerShot::where('room_id', $room->id)->delete();
        DB::commit();

        return "Game has been restarted, place your ships again!";
    }

    private function checkOwner(int $ownerId, int $userId): bool
    {
        if ($ownerId !== $userId) throw new UserNotAuthorizedException;

        return true;
    }

    private function userOwnsRoom(int $userId): bool
    {
        return Room::where('owner_id', $userId)->exists();
    }

    private function isUserInRoom(int $userId): bool
    {
        if (!Room::where('player_id', $userId)->exists()) return false;

        $this->userLeaveRoom(Room::where('player_id', $userId)->first());
        return false;
    }

    private function userLeaveRoom(Room $room): bool
    {
        return $room->update([
            'player_id' => null,
            'is_open' => 1,
        ]);
    }
}