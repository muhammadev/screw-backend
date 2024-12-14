<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class GamePlayerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your CSV file
        $csvFilePath = base_path('database/seeders/data/game-player-table.csv');

        // Read the CSV file using League\Csv\Reader
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // Assuming the CSV has a header row

        // Iterate through each row in the CSV
        foreach ($csv as $row) {
            // Insert data into the `game_player` table
            DB::table('game_player')->insert([
                'id'        => $row['id'],
                'game_id' => $row['game_id'],
                'player_id' => $row['player_id'],
                'score'     => $row['score'],
                'winner'    => $row['winner'],
                'screwed'   => $row['screwed'],
                'created_at' => now(), // Set timestamps (optional)
                'updated_at' => now(),
            ]);
        }
    }
}
