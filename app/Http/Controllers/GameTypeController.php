<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameType;

class GameTypeController extends Controller
{
    public function index()
    {
        $types = GameType::all();
        return response()->json($types);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $gameType = GameType::create(['type' => $validated['name']]);

        return response()->json([
            'message' => 'Game Type created successfully!',
            'game_type' => $gameType
        ]);
    }
}
