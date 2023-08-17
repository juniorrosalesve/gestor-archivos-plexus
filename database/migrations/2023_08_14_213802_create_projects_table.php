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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('regionId');
            $table->foreign('regionId')->references('id')->on('regions');
            $table->unsignedBigInteger('countryId');
            $table->foreign('countryId')->references('id')->on('countries');
            $table->unsignedBigInteger('managerId');
            $table->foreign('managerId')->references('id')->on('users');
            $table->date('delivery')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
