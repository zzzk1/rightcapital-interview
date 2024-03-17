<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * This class use to call sub seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * This method use to invoke sub seeder
     */
    public function run(): void
    {
        $this->call(NoteSeeder::class);
        $this->call(TagSeeder::class);
    }
}
