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
        Schema::disableForeignKeyConstraints();

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('duration');

            $table->string('availability');
            $table->enum('type', ["private", "public"]);

            $table->string('banner');
            $table->string('thumbnail');

            $table->json('trip_information')->nullable();
            $table->json('before_you_go')->nullable();

            $table->integer('max_people')->nullable();

            $table->integer('price_start');
            $table->boolean('has_offer')->default(false);
            $table->integer('offer_price')->nullable();

            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories');

            $table->bigInteger('pickup_location_id')->unsigned();
            $table->foreign('pickup_location_id')->references('id')->on('locations');

            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('deleted_by')->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
