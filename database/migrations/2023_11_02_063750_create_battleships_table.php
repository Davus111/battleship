<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battleships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('length');
        });

        Artisan::call('db:seed', array('--class' => 'BattleshipsSeeder'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battleships');
    }
};
