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
            // Métricas de PageSpeed Insights
            $table->integer('pagespeed_score_mobile')->nullable()->after('ttfb');
            $table->integer('pagespeed_score_desktop')->nullable()->after('pagespeed_score_mobile');

            // Core Web Vitals - Mobile
            $table->decimal('fcp_mobile', 8, 2)->nullable()->after('pagespeed_score_desktop'); // First Contentful Paint
            $table->decimal('lcp_mobile', 8, 2)->nullable()->after('fcp_mobile'); // Largest Contentful Paint
            $table->decimal('cls_mobile', 8, 4)->nullable()->after('lcp_mobile'); // Cumulative Layout Shift
            $table->decimal('fid_mobile', 8, 2)->nullable()->after('cls_mobile'); // First Input Delay
            $table->decimal('tti_mobile', 8, 2)->nullable()->after('fid_mobile'); // Time to Interactive

            // Core Web Vitals - Desktop
            $table->decimal('fcp_desktop', 8, 2)->nullable()->after('tti_mobile');
            $table->decimal('lcp_desktop', 8, 2)->nullable()->after('fcp_desktop');
            $table->decimal('cls_desktop', 8, 4)->nullable()->after('lcp_desktop');
            $table->decimal('fid_desktop', 8, 2)->nullable()->after('cls_desktop');
            $table->decimal('tti_desktop', 8, 2)->nullable()->after('fid_desktop');

            // Recomendaciones de optimización (JSON)
            $table->json('pagespeed_recommendations')->nullable()->after('tti_desktop');
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
            $table->dropColumn([
                'pagespeed_score_mobile',
                'pagespeed_score_desktop',
                'fcp_mobile',
                'lcp_mobile',
                'cls_mobile',
                'fid_mobile',
                'tti_mobile',
                'fcp_desktop',
                'lcp_desktop',
                'cls_desktop',
                'fid_desktop',
                'tti_desktop',
                'pagespeed_recommendations',
            ]);
        });
    }
};
