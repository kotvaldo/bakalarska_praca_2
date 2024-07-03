<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('control_points', function (Blueprint $table) {
            $table->id();
            $table->string("data_type");
            $table->integer('latitude');
            $table->integer('longitude');
            $table->unsignedBigInteger('drone_id')->nullable();
            $table->foreign('drone_id')->references('id')->on('drones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_points');
    }
};
