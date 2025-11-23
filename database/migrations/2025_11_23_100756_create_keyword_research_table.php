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
        Schema::create('keyword_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->integer('search_volume')->nullable(); // Volumen de búsqueda estimado
            $table->decimal('difficulty', 5, 2)->nullable(); // Dificultad (0-100)
            $table->decimal('cpc', 8, 2)->nullable(); // Costo por clic estimado
            $table->string('intent')->nullable(); // informational, navigational, transactional, commercial
            $table->integer('current_position')->nullable(); // Posición actual del sitio (si rankea)
            $table->integer('clicks')->nullable(); // Clics desde GSC
            $table->integer('impressions')->nullable(); // Impresiones desde GSC
            $table->decimal('ctr', 5, 2)->nullable(); // CTR desde GSC
            $table->string('source')->default('manual'); // manual, gsc, autocomplete, competitor
            $table->text('notes')->nullable();
            $table->boolean('is_tracked')->default(false); // Si ya está en la tabla keywords
            $table->timestamps();

            $table->index(['site_id', 'keyword']);
            $table->index(['site_id', 'is_tracked']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_research');
    }
};
