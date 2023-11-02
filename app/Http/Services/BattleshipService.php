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
use Illuminate\Support\Facades\DB;

class BattleshipService
{

    public function createPlayerBattleship($request, $room) 
    {
        DB::beginTransaction();
        if (!PlayerBattleship::where('battleship_id', $request->input('battleship_id'))->where('player_id', $request->user->id)->exists()) {
            PlayerBattleship::create([
                'room_id' => $room->id,
                'player_id' => $request->user->id,
                'battleship_id' => $request->input('battleship_id'),
            ]);
        };
        $playerBattleship = PlayerBattleship::with('battleship')->where('battleship_id', $request->input('battleship_id'))->where('player_id', $request->user->id)->first();
        
        $this->setPlayerBattleship($playerBattleship, $request, $room);
        DB::commit();

        return "Ship placed succesfully";
    }

    public function shoot($request, $room) {
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

        if ($battleship !== null) {
            foreach ($battleship->battleshipPositions as $position) {
                if ($position->field === $shot_field) {
                    $position->is_hit = 1;
                    $position->save();
                };
                $position->is_hit === 1 ? $hits++ : '' ;
            }
            if ($battleship->battleship->length === $hits) {
                $battleship->is_destroyed = 1;
                $battleship->save();
                $message = "Hit and sunk! Enemy has " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Your turn again";
            } else {
                $message = "Hit! Enemy has " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Your turn again";
            };
        } else {
            $room->player_turn = $defender;
            $room->save();
            $message = "Miss! " . $this->getPlayerAliveBattleshipsAmount($room->id, $defender) . " ships left! Opponent turn";
        };
        DB::commit();
        if ($this->getPlayerAliveBattleshipsAmount($room->id, $defender) === 0) {
            $message = "Every enemy ship destroyed! You Won!";
        }

        return $message;
    }

    private function setPlayerBattleship($battleship, $request, $room)
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
        return 1;
    }

    private function deleteBattleships($battleship_id)
    {
        $battleships = BattleshipPosition::where('player_battleship_id', $battleship_id)->get();
        if (count($battleships) > 0) {
            foreach ($battleships as $battleship) {
                $battleship->delete();
            };
        }

        return 1;
    }

    private function checkIfFieldAvailable($room_id, $player_id, $field) {
        $battleships = $this->getPlayerBattleships($room_id, $player_id);
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

    private function getPlayerBattleships($room_id, $player_id) 
    {
        return PlayerBattleship::with('battleshipPositions')->where('room_id', $room_id)->where('player_id', $player_id)->get();
    }

    public function getPlayerAliveBattleshipsAmount($room_id, $player_id) 
    {
        return PlayerBattleship::where('room_id', $room_id)->where('player_id', $player_id)->where('is_destroyed', 0)->count();
    }

    private function checkTurn($room, $player_id) 
    {
        if ($room->player_turn === null) {
            throw new GameHasntStartedException;
        } else if ($room->player_turn === $player_id) {
            return true;
        } else {
            throw new NotYourTurnException;
        }
    }

    private function checkShot($room, $player_id, $field) 
    {
        if (PlayerShot::where('room_id', $room)->where('player_id', $player_id)->where('field', $field)->first() === null) {
            PlayerShot::create([
                'room_id' => $room,
                'player_id' => $player_id,
                'field'=> $field,
            ]);

            return true;
        } else {
            throw new FieldAlreadyShotException;
        }
    }
}