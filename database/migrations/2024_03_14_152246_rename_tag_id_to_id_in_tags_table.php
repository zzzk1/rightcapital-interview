<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename table tag primary key.
     */
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('tag_id', 'id');
        });
    }

    /**
     * Undo rename table tag primary key.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('id', 'tag_id');
        });
    }
};
