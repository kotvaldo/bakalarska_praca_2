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
        Schema::create('data_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('control_point_id')->nullable()->constrained('control_points')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('drone_id')->nullable()->constrained('drones')->onDelete('set null')->onUpdate('cascade');
            $table->integer('data_quality')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_records');
    }
};
