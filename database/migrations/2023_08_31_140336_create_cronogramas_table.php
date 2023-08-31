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
        Schema::create('cronogramas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projectId');
            $table->foreign('projectId')->references('id')->on('projects');
            $table->string('n_factura');
            $table->date('fecha_factura');
            $table->date('fecha_vencimiento');
            $table->date('fecha_pagoreal')->nullable();
            $table->string('moneda');
            $table->double('monto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronogramas');
    }
};
