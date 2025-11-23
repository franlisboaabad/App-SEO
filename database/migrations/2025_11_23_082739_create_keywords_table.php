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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('keyword'); // La keyword a seguir
            $table->string('target_url')->nullable(); // URL objetivo (opcional)
            $table->integer('current_position')->nullable(); // Posición actual
            $table->integer('previous_position')->nullable(); // Posición anterior
            $table->date('last_checked')->nullable(); // Última vez que se verificó
            $table->text('notes')->nullable(); // Notas adicionales
            $table->boolean('is_active')->default(true); // Activa/Inactiva
            $table->timestamps();

            // Índices
            $table->index(['site_id', 'keyword']);
            $table->unique(['site_id', 'keyword']); // Una keyword única por sitio
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keywords');
    }
};
