<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;

class updateGameWinnerAndScrewed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-game-winner-and-screwed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update winner and screwed fields for all games';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // fetch all games
        $games = Game::all();

        foreach ($games as $game) {
            $this->updateGameWinnerAndScrewed($game->id);

            $this->info('Game winner and screwed fields updated successfully.');
        }
    }

    private function updateGameWinnerAndScrewed($gameId)
    {
        $game = Game::with('players')->findOrFail($gameId);

        $players = $game->players;

        if ($players->isEmpty()) {
            $this->warn("No players found for game ID $gameId.");
            return;
        }

        $minScore = $players->min('pivot.score');
        $maxScore = $players->max('pivot.score');

        foreach ($players as $player) {
            $game->players()->updateExistingPivot($player->id, [
                'winner' => $player->score === $minScore,
                'screwed' => $player->score === $maxScore
            ]);
        }
    }
}
