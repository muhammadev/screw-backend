<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

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
        // retrieve the Individual game type
        $individualGameType = GameType::where('type', 'Individual')->firstOrFail();

        // validate the request
        $validated = $this->validateRequest($request, $individualGameType);

        // set the default game type id if not provided
        $gameTypeId = $validated['game_type_id'];

        // create a new game record
        $game = Game::create(['game_type_id' => $gameTypeId]);

        // handle game logic: individual or team based game
        $this->handleGameCreation($game, $validated);

        $relationships = [];

        if ($game->gameType->type === "Individual") {
            $relationships = ['players'];
        } elseif ($game->gameType->type === "Team") {
            $relationships = ['teams.players'];
        }

        return response()->json($game->load($relationships), 201);
    }

    protected function validateRequest(Request $request, $individualGameType)
    {
        $rules = [
            'game_type_id' => 'required|exists:App\Models\GameType,id',
        ];

        $gameTypeId = $request->input('game_type_id');

        if ($gameTypeId == $individualGameType->id) {
            $rules['players'] = 'required|array';
            $rules['players.*.id'] = 'exists:players,id';
            $rules['players.*.score'] = 'required|numeric';
        } else {
            $rules['teams'] = 'required|array';
            $rules['teams.*.id'] = 'exists:teams,id';
            $rules['teams.*.score'] = 'required|numeric';
            $rules['teams.*.penalty'] = 'required|boolean';
            $rules['teams.*.players'] = 'required|array';
            $rules['teams.*.players.*.id'] = 'exists:players,id';
            $rules['teams.*.players.*.score'] = 'required|numeric';
            $rules['teams.*.players.*.penalty'] = 'required|boolean';
        }

        return Validator::make($request->all(), $rules)->validate();
    }

    protected function handleGameCreation(Game $game, array $validated)
    {
        $gameType = $game->gameType;

        if ($gameType->type == 'Individual') {
            $this->attachEntitiesToGame($game, $validated['players'], false);
        } elseif ($gameType->type == 'Team') {
            $this->attachEntitiesToGame($game, $validated['teams'], true);
        }
    }

    protected function attachEntitiesToGame(Game $game, array $entities, bool $isTeams)
    {
        $lowestScore = collect($entities)->min('score');
        $maxScore = collect($entities)->max('score');

        foreach ($entities as $entity) {
            if ($isTeams) {
                $isWinner = $entity['score'] === $lowestScore;
                $isScrewed = $entity['score'] === $maxScore;

                $hasPenalty = collect($entity['players'])->contains(function ($player) {
                    return $player['penalty'] ?? false;
                });

                $game->teams()->attach($entity['id'], [
                    'score' => $entity['score'],
                    'winner' => $isWinner,
                    'screwed' => $isScrewed,
                    'penalty' => $hasPenalty,
                ]);

                $this->attachPlayersToGame($game, $entity['players']);
            } else {
                $this->attachPlayersToGame($game, $entities);
            }
        }
    }
    protected function attachPlayersToGame(Game $game, array $players)
    {
        $lowestScore = collect($players)->min('score');
        $maxScore = collect($players)->max('score');

        foreach ($players as $player) {
            $isWinner = $player['score'] === $lowestScore;
            $isScrewed = $player['score'] === $maxScore;

            $game->players()->attach($player['id'], [
                'score' => $player['score'],
                'winner' => $isWinner,
                'screwed' => $isScrewed,
                'penalty' => $player->penalty,
            ]);
        }
    }


    public function update(Request $request, $id)
    {
        // retrieve the Individual game type
        $individualGameType = GameType::where('type', 'Individual')->firstOrFail();

        // validate the request
        $validated = $this->validateRequest($request, $individualGameType);

        $game = Game::where('id', $id)->firstOrFail();

        $this->handleUpdateLogic($game, $validated);

        return response()->json($game->load('players'), 200);
    }

    protected function handleUpdateLogic(Game $game, array $validated)
    {
        $gameType = $game->gameType->type;

        if ($gameType == "Individual") {
            $this->syncEntitiesToGame($game, $validated['players']);
        } elseif ($gameType == "Team") {
            $this->syncEntitiesToGame($game, $validated['teams'], true);
        }
    }

    protected function syncEntitiesToGame(Game $game, array $entities, bool $isTeams = false)
    {
        $lowestScore = collect($entities)->min('score');
        $maxScore = collect($entities)->max('score');

        foreach ($entities as $entity) {
            if ($isTeams) {
                // TEAM UPDATES
                $isWinner = $entity['score'] === $lowestScore;
                $isScrewed = $entity['score'] === $maxScore;

                // syncWithoutDetaching -> because the syncing is happening on each team iteration
                $game->teams()->syncWithoutDetaching([
                    $entity['id'] => [
                        'score' => $entity['score'],
                        'winner' => $isWinner,
                        'screwed' => $isScrewed,
                    ]
                ]);

                $this->syncPlayersToGame($game, $entity['players']);
            } else {
                // INDIVIDUAL UPDATES
                $this->syncPlayersToGame($game, $entities);
            }
        }
    }

    protected function syncPlayersToGame(Game $game, $players)
    {
        $minScore = collect($players)->min('score');
        $maxScore = collect($players)->max('score');

        $syncData = collect($players)->mapWithKeys(function ($player) use ($minScore, $maxScore) {
            $isWinner = $player['score'] === $minScore;
            $isScrewed = $player['score'] === $maxScore;
            return [
                $player['id'] => [
                    'score' => $player['score'],
                    'winner' => $isWinner,
                    'screwed' => $isScrewed,
                    'penalty' => $player['penalty']
                ]
            ];
        });

        $game->players()->sync($syncData);
    }

    public function destroy($id)
    {
        try {

            $game = Game::findOrFail($id);

            $game->delete();

            return response()->json(['message' => 'Game deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) { // Catch other potential exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the game.',
                'error' => $e->getMessage(), // Optionally include the error message for debugging (remove in production)
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        }
    }
}
