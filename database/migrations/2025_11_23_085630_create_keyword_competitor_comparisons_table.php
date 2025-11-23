<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabla para comparar keywords entre sitio y competidores
        Schema::create('keyword_competitor_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade');
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->integer('competitor_position')->nullable(); // Posición del competidor para esta keyword
            $table->date('date'); // Fecha de la comparación
            $table->integer('position_gap')->nullable(); // Diferencia de posiciones (tu posición - posición competidor)
            $table->timestamps();

            // Índices con nombres cortos
            $table->index(['keyword_id', 'competitor_id', 'date'], 'kcc_idx');
            $table->unique(['keyword_id', 'competitor_id', 'date'], 'kcc_unique'); // Una comparación única por keyword/competidor/fecha
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_competitor_comparisons');
    }
};
