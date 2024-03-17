<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;

/**
 * Populate data using note model factory
 */
class NoteSeeder extends Seeder
{
    /**
     * Insert fake note data to database.
     */
    public function run(): void
    {
        Note::factory(10)->create();
    }
}
