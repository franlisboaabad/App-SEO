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
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade'); // Sitio al que pertenece este competidor
            $table->string('nombre'); // Nombre del competidor
            $table->string('dominio_base'); // Dominio del competidor
            $table->string('gsc_property')->nullable(); // GSC property del competidor (opcional)
            $table->json('gsc_credentials')->nullable(); // Credenciales GSC (opcional)
            $table->text('notes')->nullable(); // Notas adicionales
            $table->boolean('is_active')->default(true); // Activo/Inactivo
            $table->timestamps();

            // Índices
            $table->index('site_id');
            $table->unique(['site_id', 'dominio_base']); // Un competidor único por sitio
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitors');
    }
};
