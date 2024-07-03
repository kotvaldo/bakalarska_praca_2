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
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('serial_number');
            $table->unsignedBigInteger('mission_id')->nullable()->default(null);
            $table->foreign('mission_id')->references('id')->on('missions');
            $table->string('image')->nullable();
            $table->integer('cp_count')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drones');
    }
};
