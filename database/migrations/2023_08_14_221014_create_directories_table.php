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
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projectId');
            $table->foreign('projectId')->references('id')->on('projects');
            $table->string('name');
            $table->integer('route')->default(0);
            $table->bigInteger('link')->default(0);
            $table->string('file_path')->nullable();
            $table->string('file_ext')->nullable();
            $table->string('type')->default('directory');
            $table->integer('week_from')->default(0);
            $table->integer('week_to')->default(0);
            $table->integer('file_week')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directories');
    }
};
