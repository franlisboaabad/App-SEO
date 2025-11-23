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
        Schema::create('seo_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('url')->nullable(); // URL de la página
            $table->string('keyword')->nullable(); // Palabra clave
            $table->string('device')->default('desktop'); // desktop, mobile, tablet
            $table->date('date'); // Fecha de la métrica
            $table->integer('clicks')->default(0); // Clics
            $table->integer('impressions')->default(0); // Impresiones
            $table->decimal('ctr', 5, 4)->default(0); // Click-through rate (0.0000 a 1.0000)
            $table->decimal('position', 5, 2)->default(0); // Posición promedio
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['site_id', 'date']);
            $table->index(['site_id', 'url']);
            $table->index(['site_id', 'keyword']);
            $table->index('date');

            // Índice único para evitar duplicados
            $table->unique(['site_id', 'url', 'keyword', 'device', 'date'], 'unique_metric');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_metrics');
    }
};
