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
        Schema::create('audit_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_audit_id')->constrained('seo_audits')->onDelete('cascade');

            // SEO On-Page
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('h1_count')->default(0);
            $table->integer('h2_count')->default(0);
            $table->integer('h3_count')->default(0);
            $table->integer('images_without_alt')->default(0);
            $table->integer('images_total')->default(0);
            $table->string('canonical')->nullable();
            $table->string('robots_meta')->nullable();

            // Links
            $table->integer('internal_links_count')->default(0);
            $table->integer('external_links_count')->default(0);
            $table->integer('broken_links_count')->default(0);

            // Performance
            $table->decimal('ttfb', 8, 3)->nullable(); // Time to First Byte en segundos

            // Status Code
            $table->integer('status_code')->nullable();

            // Errores y advertencias
            $table->json('errors')->nullable(); // Array de errores encontrados
            $table->json('warnings')->nullable(); // Array de advertencias

            $table->timestamps();

            $table->index('seo_audit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_results');
    }
};
