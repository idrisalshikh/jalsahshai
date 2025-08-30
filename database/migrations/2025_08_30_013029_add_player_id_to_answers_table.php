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
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('player_id')->constrained()->onDelete('cascade')->after('id');
            $table->dropForeign(['game_session_id']);
            $table->dropColumn(['nickname', 'game_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropColumn('player_id');
            $table->string('nickname');
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
        });
    }
};
