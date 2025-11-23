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
        Schema::create('serp_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('keyword_id')->nullable()->constrained('keywords')->onDelete('set null');
            $table->string('keyword'); // Keyword analizada
            $table->integer('position')->nullable(); // Posición del sitio en SERP
            $table->string('url')->nullable(); // URL que aparece en SERP
            $table->string('title')->nullable(); // Título del snippet
            $table->text('description')->nullable(); // Descripción del snippet
            $table->string('display_url')->nullable(); // URL mostrada en SERP
            $table->json('competitors')->nullable(); // Top 10 resultados (competidores)
            $table->json('features')->nullable(); // Features de SERP (rich snippets, featured snippet, etc.)
            $table->text('suggestions')->nullable(); // Sugerencias de mejora
            $table->date('analysis_date'); // Fecha del análisis
            $table->timestamps();

            // Índices
            $table->index(['site_id', 'keyword']);
            $table->index(['site_id', 'keyword_id']);
            $table->index('analysis_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serp_analyses');
    }
};
