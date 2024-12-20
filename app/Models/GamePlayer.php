<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GamePlayer extends Pivot
{
    use HasFactory;

    protected $fillable = [
        "game_id",
        "player_id",
        "score",
        "winner",
        "screwed"
    ];
}
