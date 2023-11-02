<?php

namespace App\Http\Services;

use App\Exceptions\AnotherShipIsOnFieldException;
use App\Exceptions\BadPlacementException;
use App\Exceptions\FieldAlreadyShotException;
use App\Exceptions\GameHasntStartedException;
use App\Exceptions\NotYourTurnException;
use App\Exceptions\ShipTooCloseException;
use App\Models\BattleshipPosition;
use App\Models\PlayerBattleship;
use App\Models\PlayerShot;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BattleshipService
{

    public function createPlayerBattleship(Request $request, Room $room): string
    {
        DB::beginTransaction();
        $playerBattleship = PlayerBattleship::firstOrCreate([
            'room_id' => $room->id,
            'player_id' => $request->user->id,
            'battleship_id' => $request->input('battleship_id'),
        ],[]);
        
        $this->setPlayerBattleship($playerBattleship, $request, $room);
        DB::commit();

        return "Ship placed succesfully";
    }

    public function shoot(Request $request, Room $room): string
    {
        $shot_field = $request->input('shoot');
        $message = '';
        $hits = 0;
        $shooter = $request->user->id;
        $defender = $room->owner_id === $shooter ? $room->player_id : $room->owner_id;

        $this->checkTurn($room, $shooter);
        DB::beginTransaction();
        $this->checkShot($room->id, $shooter, $shot_field);
        $battleship = PlayerBattleship::with('battleshipPositions', 'battleship')
            ->where('room_id', $room->id)
            ->where('player_id', $defender)
            ->whereHas('battleshipPositions', function ($query) use ($shot_field) {
                $query->where('field', $shot_field);
            })
            ->first();

        if (!is_null($battleship)) {
            foreach ($battleship->battleshipPositions as $position) {
                if ($position->field === $shot_field) $position->update(['is_hit' => 1]);
                if ($position->is_hit === 1) $hits++;
            }
            if ($battleship->battleship->length === $hits) {
                $battleship->update(['is_destroyed' => 1]);
                $message = "Hit and sunk! Enemy has " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Your turn again";
            } else {
                $message = "Hit! Enemy has " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Your turn again";
            };
        } else {
            $room->update(['player_turn' => $defender]);
            $message = "Miss! " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Opponent turn";
        };
        DB::commit();
        if ($this->getPlayerAliveBattleshipsAmount($room->id, $defender) === 0) {
            $room->update(['player_turn' => null]);
            $message = "Every enemy ship destroyed! You Won!";
        }

        return $message;
    }

    private function setPlayerBattleship(PlayerBattleship $battleship, Request $request, Room $room): bool
    {
        $gridY = range('A', 'J');
        $gridX = range(1, 10);
        $letter = substr($request->input('field'), 0, 1);
        $number = (int) substr($request->input('field'), 1);
        $rotation = $request->input('rotation');
        $battleshipLength = $battleship->battleship->length;
        
        $this->deleteBattleships($battleship->id);
        
        if ($rotation == 1) {
            $index = array_search($letter, $gridY);
            
            for ($i = $index; $i < $index + $battleshipLength; $i++) {
                if (in_array($number, $gridX) && $i < 10 && $this->checkIfFieldAvailable($room->id, $request->user->id, $gridY[$i] . $number)) {
                    BattleshipPosition::create([
                        'player_battleship_id' => $battleship->id,
                        'field' => $gridY[$i] . $number
                    ]);
                } else {
                    throw new BadPlacementException;
                }
            }
        } else {
            $index = array_search($number, $gridX);
            
            for ($i = $index; $i < $index + $battleshipLength; $i++) {
                if (in_array($letter, $gridY) && $i < 10 && $this->checkIfFieldAvailable($room->id, $request->user->id, $letter . $gridX[$i])) {
        
                    BattleshipPosition::create([
                        'player_battleship_id' => $battleship->id,
                        'field' =>  $letter . $gridX[$i]
                    ]);
                } else {
                    throw new BadPlacementException;
                }
            }
        }
        return true;
    }

    private function deleteBattleships(int $battleshipId): bool
    {
        $battleships = BattleshipPosition::where('player_battleship_id', $battleshipId)->delete();

        return true;
    }

    private function checkIfFieldAvailable(int $roomId, int $playerId, string $field): bool
    {
        $battleships = PlayerBattleship::with('battleshipPositions')->where('room_id', $roomId)->where('player_id', $playerId)->get();
        foreach ($battleships as $battleship) {
            foreach ($battleship->battleshipPositions as $position) {
                if ($position->field === $field) {
                    throw new AnotherShipIsOnFieldException;
                // } else if () {
                    // throw new ShipTooCloseException;
                }
            }
        }
        return true;
    }

    public function getPlayerAliveBattleshipsAmount(int $roomId, int $playerId): int
    {
        return PlayerBattleship::where('room_id', $roomId)->where('player_id', $playerId)->where('is_destroyed', 0)->count();
    }

    private function checkTurn(Room $room, int $playerId): bool
    {
        if ($room->player_turn === null) throw new GameHasntStartedException;
        if ($room->player_turn !== $playerId) throw new NotYourTurnException;
            
        return true;
    }

    private function checkShot(int $roomId, int $playerId, string $field): bool
    {
        if(!is_null(PlayerShot::where('room_id', $roomId)->where('player_id', $playerId)->where('field', $field)->first())) {
            throw new FieldAlreadyShotException;
        }

        PlayerShot::create([
            'room_id' => $roomId,
            'player_id' => $playerId,
            'field'=> $field,
        ]);

        return true;
    }
}