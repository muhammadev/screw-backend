<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the CSV file
        $csvFilePath = database_path('seeders/data/games-table.csv');

        // Load the CSV file
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // The header row is at index 0

        // Loop through each record and insert into the database
        foreach ($csv as $record) {
            DB::table('games')->insert([
                'id'           => $record['id'],
                'game_type_id' => $record['game_type_id'],
                'created_at'   => $record['created_at'],
                'updated_at'   => $record['updated_at'],
            ]);
        }
    }
}
