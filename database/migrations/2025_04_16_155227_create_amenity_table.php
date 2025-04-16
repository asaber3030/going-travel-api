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
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->boolean('free_wifi')->default(0)->nullable();
            $table->boolean('spa_wellness_center')->default(0)->nullable();
            $table->boolean('fitness_center')->default(0)->nullable();
            $table->boolean('gourmet_restaurant')->default(0)->nullable();
            $table->boolean('indoor_outdoor_pools')->default(0)->nullable();
            $table->boolean('air_conditioning')->default(0)->nullable();
            $table->boolean('flat_screen_tv')->default(0)->nullable();
            $table->boolean('free_parking')->default(0)->nullable();
            $table->boolean('front_desk_24h')->default(0)->nullable();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity');
    }
};
