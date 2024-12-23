<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_team', function (Blueprint $table) {
            $table->integer('score')->default(0);
            $table->boolean('winner')->default(false);
            $table->boolean('screwed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_team', function (Blueprint $table) {
            $table->dropColumn('score');
            $table->dropColumn('winner');
            $table->dropColumn('screwed');
        });
    }
};
