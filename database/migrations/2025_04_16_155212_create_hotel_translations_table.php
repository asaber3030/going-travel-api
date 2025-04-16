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
        Schema::create('hotel_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->text('address')->nullable();
            $table->text('policy')->nullable();
            $table->text('room_types')->nullable();
            $table->string('slug', 255)->nullable();
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
        Schema::dropIfExists('hotel_translations');
    }
};
