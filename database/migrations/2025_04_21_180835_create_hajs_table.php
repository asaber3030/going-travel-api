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
        Schema::create('hajs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->text('long_description');

            $table->decimal('price', 10, 2);
            $table->string('banner')->nullable();
            $table->string('thumbnail')->nullable();

            $table->string('hotel');
            $table->string('meals');
            $table->string('transportation_type');
            $table->date('depature_date');
            $table->date('return_date');

            $table->string('notes');
            $table->string('cautions');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hajs');
    }
};
