<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['game_type_id'];

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withPivot("score", "winner", "screwed")
            ->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'game_team')
            ->withPivot(['score', 'winner', 'screwed'])
            ->withTimestamps();
    }

    public function gameType() {
        return $this->belongsTo(GameType::class, 'game_type_id');
    }
}
