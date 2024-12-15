<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $appends = [];
    // protected $hidden = ["pivot"];

    // public function getScoreAttribute()
    // {
    //     return $this->pivot->score ?? null;
    // }


    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_player')
            ->withPivot('score');
    }
}
