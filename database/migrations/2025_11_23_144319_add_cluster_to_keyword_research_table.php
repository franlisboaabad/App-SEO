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
        Schema::table('keyword_research', function (Blueprint $table) {
            $table->string('cluster')->nullable()->after('intent'); // Grupo/tema al que pertenece la keyword
            $table->integer('trend_score')->nullable()->after('search_volume'); // Score de tendencia desde Google Trends
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keyword_research', function (Blueprint $table) {
            $table->dropColumn(['cluster', 'trend_score']);
        });
    }
};
