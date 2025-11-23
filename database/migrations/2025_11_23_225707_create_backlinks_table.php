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
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('source_domain'); // Dominio que enlaza
            $table->text('source_url'); // URL exacta que enlaza
            $table->text('target_url'); // URL de destino en nuestro sitio
            $table->text('anchor_text')->nullable(); // Texto del enlace
            $table->enum('link_type', ['dofollow', 'nofollow', 'sponsored', 'ugc'])->default('dofollow');
            $table->date('first_seen')->nullable(); // Primera vez que se detectó
            $table->date('last_seen')->nullable(); // Última vez que se detectó
            $table->integer('domain_authority')->nullable(); // Autoridad del dominio (si se obtiene)
            $table->integer('page_authority')->nullable(); // Autoridad de la página (si se obtiene)
            $table->enum('source_type', ['gsc', 'manual', 'api_ahrefs', 'api_semrush', 'api_moz'])->default('manual');
            $table->boolean('is_toxic')->default(false); // Marca si es un backlink tóxico
            $table->text('toxic_reason')->nullable(); // Razón por la que se marca como tóxico
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();

            // Índices
            $table->index('site_id');
            $table->index('source_domain');
            $table->index('is_toxic');
            $table->index('source_type');
            $table->index(['site_id', 'source_domain']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backlinks');
    }
};
