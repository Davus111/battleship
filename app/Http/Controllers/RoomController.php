<?php

namespace App\Http\Controllers;

use App\Models\Room;
use DB;
use Illuminate\Http\Request;
use App\Http\Services\RoomService;

class RoomController extends Controller
{
    public function __construct(private RoomService $roomService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Contains all lobbies for user to choose
        try {
            return Room::all();
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return response()->json($this->roomService->createRoom($request->user));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //There we can return Grid (if necesary) and available battleships
        try {
            return $room;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function join(Request $request, Room $room)
    {
        try {
            return response()->json($this->roomService->join($request->user->id, $room));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function leave(Request $request, Room $room)
    {
        try {
            return response()->json($this->roomService->leave($request->user->id, $room));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function start(Request $request, Room $room) {
        try {
            return response()->json($this->roomService->startRoom($room, $request->user->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function restart(Request $request, Room $room) {
        try {
            return response()->json($this->roomService->restartRoom($room, $request->user->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}