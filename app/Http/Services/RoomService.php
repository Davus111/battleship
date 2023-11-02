<?php

namespace App\Http\Services;

use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RoomService
{

    public function createRoom($request) 
    {
        if ($this->userOwnsRoom($request->user->id)) {
            return response()->json(['message' => 'You already own a room'], 403);
        } else {
            try {
                DB::beginTransaction();
                Room::create([
                    'owner_id' => $request->user->id,
                ]);
            } catch (QueryException $e) {
                DB::rollBack();
                return response()->json(['message' => 'An error occurred while creating a room'], 500);
            }
            DB::commit();
        }

        return response()->json(['message' => 'Room created successfully'], 200);
    }
    

    public function join($request, $room)
    {
        $user_id = $request->user->id;

        if ($this->userOwnsRoom($user_id)) {
            return response()->json(['message' => 'You already own a room'], 403);
        } else if (!$room->is_open) {
            return response()->json(['message' => 'This room is full'], 403);
        } else {
            try {
                DB::beginTransaction();
                $this->userIsInRoom($user_id);
                $room->update([
                    'player_id' => $user_id,
                    'is_open' => 0,
                ]);
            } catch (QueryException $e) {
                DB::rollBack();
                return response()->json(['message' => 'An error occurred while joining the room'], 500);
            }
            DB::commit();
        }

        return response()->json(['message' => 'Room joined successfully'], 200);
    }

    public function leave($request, $room)
    {
        $user_id = $request->user->id;

        try {
            DB::beginTransaction();
            if ($this->userOwnsRoom($user_id)) {
                $room->delete();
                DB::commit();

                return response()->json(['message' => 'You left and removed the room succesfully'], 200);
            } else {
                $this->userLeaveRoom($room);
                DB::commit();

                return response()->json(['message' => 'You left the room'], 200);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while leaving the room'], 500);
        }
    }

    private function userOwnsRoom($userId)
    {
        return Room::where('owner_id', $userId)->exists();
    }

    private function userIsInRoom($userId)
    {
        if (Room::where('player_id', $userId)->exists()) {
            $this->userLeaveRoom(Room::where('player_id', $userId)->first());
        }

        return 1;
    }

    private function userLeaveRoom($room)
    {
        $room->update([
            'player_id' => null,
            'is_open' => 1,
        ]);

        return 1;
    }
}