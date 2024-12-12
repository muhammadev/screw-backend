<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $appends = ["number_of_games", "winner_count", "screwed_count"];
    // protected $hidden = ["pivot"];

    public function getScoreAttribute()
    {
        return $this->pivot->score ?? null;
    }

    public function numberOfGames(): Attribute {
        return Attribute::make(
            get: fn () => $this->games()->count()
        );
    }

    public function winnerCount(): Attribute {
        return Attribute::make(
            get: fn () => $this->games()->wherePivot('winner', true)->count()
        );
    }

    public function screwedCount(): Attribute {
        return Attribute::make(
            get: fn () => $this->games()->wherePivot('screwed', true)->count()
        );
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)
                    ->withPivot('score');
    }
}
