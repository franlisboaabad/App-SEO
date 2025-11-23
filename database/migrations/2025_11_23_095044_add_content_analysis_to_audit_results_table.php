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
        Schema::table('audit_results', function (Blueprint $table) {
            // Análisis de contenido
            $table->integer('word_count')->nullable()->after('robots_meta');
            $table->text('keyword_density')->nullable()->after('word_count'); // JSON con densidad de keywords
            $table->text('content_suggestions')->nullable()->after('keyword_density'); // JSON con sugerencias
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn(['word_count', 'keyword_density', 'content_suggestions']);
        });
    }
};
