<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class PlayersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the CSV file
        $csvFilePath = database_path('seeders/data/players-table.csv');

        // Load the CSV file
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // The header row is at index 0

        // Loop through each record and insert into the database
        foreach ($csv as $record) {
            DB::table('players')->insert([
                'id'         => $record['id'],
                'name'       => $record['name'],
                'team_id'    => $record['team_id'],
                'created_at' => $record['created_at'],
                'updated_at' => $record['updated_at'],
            ]);
        }
    }
}
