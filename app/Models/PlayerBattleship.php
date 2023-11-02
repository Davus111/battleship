<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerBattleship extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'player_id',
        'battleship_id',
        'is_destroyed',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class);
    }

    public function battleship()
    {
        return $this->belongsTo(Battleship::class);
    }

    public function battleshipPositions() : HasMany
    {
        return $this->hasMany(BattleshipPosition::class);
    }
}
