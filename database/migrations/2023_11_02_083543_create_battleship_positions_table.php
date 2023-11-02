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
        Schema::create('battleship_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_battleship_id')->constrained('player_battleships')->onDelete('cascade');
            $table->string('field');
            $table->boolean('is_hit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battleship_positions');
    }
};
