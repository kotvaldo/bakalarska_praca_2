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
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(auth()->id());
            $table->foreign('user_id')->references('id')->on('users');
            $table->string("name");
            $table->string("description")->default("");
            $table->boolean("active")->default(true);
            $table->double("p0")->default(0);
            $table->double("p1")->default(0);
            $table->double("p2")->default(0);
            $table->integer("w")->default(0);
            $table->integer("z0")->default(0);
            $table->integer("z1")->default(0);
            $table->integer("z2")->default(0);
            $table->integer("zn")->default(0);
            $table->boolean("automatic")->default(false);
            $table->integer("total_cp_count")->nullable()->default(0);
            $table->integer("drones_count")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
