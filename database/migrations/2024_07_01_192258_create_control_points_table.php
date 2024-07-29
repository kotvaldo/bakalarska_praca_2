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
            $table->integer('latitude');
            $table->integer('longitude');
            $table->unsignedBigInteger('drone_id')->nullable()->default(null);
            $table->foreign('drone_id')->references('id')->on('drones');
            $table->unsignedBigInteger('mission_id')->nullable()->default(null);
            $table->foreign('mission_id')->references('id')->on('missions');
            $table->string("data_type");
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
