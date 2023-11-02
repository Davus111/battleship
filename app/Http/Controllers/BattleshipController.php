<?php

namespace App\Http\Controllers;

use App\Http\Services\BattleshipService;
use App\Models\Room;
use DB;
use Illuminate\Http\Request;

class BattleshipController extends Controller
{
    protected $battleshipService;
    public function __construct(BattleshipService $battleshipService)
    {
        $this->battleshipService = $battleshipService;
    }
    public function setBattleship(Request $request, Room $room)
    {
        $request->validate([
            'battleship_id' => 'required|min:1|max:7|numeric',
            'field' => ['required', 'regex:/^[A-J](10|[1-9])$/'],
            'rotation' => 'required',
        ]);

        try {
            return response()->json($this->battleshipService->createPlayerBattleship($request, $room));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 422);
        }
    }

    public function shoot(Request $request, Room $room)
    {
        $request->validate([
            'shoot' => ['required', 'regex:/^[A-J](10|[1-9])$/'],
        ]);

        try {
            return response()->json($this->battleshipService->shoot($request, $room));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
