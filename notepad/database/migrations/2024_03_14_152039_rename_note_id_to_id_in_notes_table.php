<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename table note index key.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->renameColumn('note_id', 'id');
        });
    }

    /**
     * Undo rename table note index key.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->renameColumn('id', 'note_id');
        });
    }
};
