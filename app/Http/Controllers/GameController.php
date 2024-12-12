<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // get all records
    public function index()
    {
        $games = Game::orderBy('id', 'desc')
            ->with('players')
            ->paginate(10);

        return response()->json(
            $games->setCollection(
                $games->getCollection()->map(function (Game $game) {
                    $game->players->transform(function ($player) {
                        $player->score = $player->pivot->score;
                        return $player;
                    });
                    return $game;
                })
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'players' => 'required|array',
            'players.*.id' => 'exists:players,id',
            'players.*.score' => 'required|numeric',
        ]);

        // Create a new game
        $game = Game::create();

        // Attach players with scores
        foreach ($validated['players'] as $player) {
            $game->players()->attach($player['id'], ['score' => $player['score']]);
        }

        return response()->json($game->load('players'), 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'players' => 'required|array',
            'players.*.id' => 'exists:players,id',
            'players.*.score' => 'required|numeric',
        ]);

        $game = Game::findOrFail($id);

        $syncData = collect($validated['players'])->mapWithKeys(function ($player) {
            return [$player['id'] => ['score' => $player['score']]];
        })->toArray();

        $game->players()->sync($syncData);

        return response()->json($game->load('players'), 200);
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);

        $game->delete();

        return response()->json(['message' => 'Game deleted successfully.']);
    }
}
