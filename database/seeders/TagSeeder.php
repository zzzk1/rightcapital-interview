<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Populate data using tag model factory
 */
class TagSeeder extends Seeder
{
    /**
     * Insert fake tag data to database.
     */
    public function run(): void
    {
        Tag::factory(10)->create();
    }
}
