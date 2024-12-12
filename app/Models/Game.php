<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withPivot("score");
    }

    public function getTestAttribute()
    {
        return 'test';
    }
}
