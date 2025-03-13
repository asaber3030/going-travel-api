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
        Schema::table('inclusions_exclusions_translations', function (Blueprint $table) {
            $table->bigInteger('exclusion_id')->unsigned();;
            $table->foreign('exclusion_id')->references('id')->on('tour_inclusions_exclusions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inclusions_exclusions_translations', function (Blueprint $table) {
            //
        });
    }
};
