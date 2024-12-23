<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name'];

    public function players()
    {
        return $this->belongsToMany(Player::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_team')
            ->withPivot(['score', 'winner', 'screwed'])
            ->withTimestamps();
    }
}
