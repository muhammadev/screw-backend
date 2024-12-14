<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your CSV file
        $csvFilePath = base_path('database/seeders/data/teams-table.csv');

        // Read the CSV file using League\Csv\Reader
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // Assuming the CSV has a header row

        // Iterate through each row in the CSV
        foreach ($csv as $row) {
            // Insert data into the `teams` table
            DB::table('teams')->insert([
                'id'         => $row['id'],
                'name'       => $row['name'],
                'created_at' => now(), // Set timestamps (optional)
                'updated_at' => now(),
            ]);
        }
    }
}
