<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattleshipPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_battleship_id',
        'field',
        'is_hit',
    ];

    public function playerBattleship()
    {
        return $this->belongsTo(PlayerBattleship::class);
    }
}
