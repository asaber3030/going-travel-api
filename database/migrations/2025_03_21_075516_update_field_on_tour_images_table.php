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
        Schema::table('tour_images', function (Blueprint $table) {
            $table->bigInteger('deleted_by')->nullable()->unsigned()->change();
            $table->bigInteger('created_by')->nullable()->unsigned()->change();
            $table->bigInteger('updated_by')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_images', function (Blueprint $table) {
            //
        });
    }
};
