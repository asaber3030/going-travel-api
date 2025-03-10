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
        Schema::create('tour_highlights_translations', function (Blueprint $table) {
            $table->id();

            $table->string('locale')->default('en');

            $table->string('title');

            $table->bigInteger('tour_highlight_id')->unsigned();
            $table->foreign('tour_highlight_id')->references('id')->on('tour_highlights')->onDelete('cascade');

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('deleted_by')->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_highlights_translations');
    }
};
