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
            // Agregar campos JSON para almacenar los links completos
            $table->json('internal_links')->nullable()->after('internal_links_count');
            $table->json('external_links')->nullable()->after('external_links_count');
            $table->json('broken_links')->nullable()->after('broken_links_count');
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
            $table->dropColumn(['internal_links', 'external_links', 'broken_links']);
        });
    }
};
