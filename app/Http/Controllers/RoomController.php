<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Services\RoomService;

class RoomController extends Controller
{
    protected $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Room::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->roomService->createRoom($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return $room;
    }

    public function join(Request $request, Room $room)
    {
        return $this->roomService->join($request, $room);
    }

    public function leave(Request $request, Room $room)
    {
        return $this->roomService->leave($request, $room);
    }
}
