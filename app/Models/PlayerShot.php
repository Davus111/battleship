<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerShot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'player_id',
        'field',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class);
    }
}
