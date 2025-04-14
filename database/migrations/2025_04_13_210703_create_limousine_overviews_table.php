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
        Schema::create('limousine_overviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('limousine_id')->nullable();
            $table->string('locale', 10)->nullable();
            $table->text('about_vehicle')->nullable();
            $table->text('key_features')->nullable();
            $table->text('available_services')->nullable();
            $table->text('pricing')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('limousine_id')->references('id')->on('limousines')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limousine_overviews');
    }
};
