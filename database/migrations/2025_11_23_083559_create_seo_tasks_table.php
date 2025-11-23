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
        Schema::create('seo_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('seo_audit_id')->nullable()->constrained('seo_audits')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title'); // Título de la tarea
            $table->text('description')->nullable(); // Descripción detallada
            $table->string('url')->nullable(); // URL relacionada

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');

            $table->date('due_date')->nullable(); // Fecha límite
            $table->timestamp('completed_at')->nullable(); // Fecha de completado

            $table->timestamps();

            // Índices
            $table->index(['site_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_tasks');
    }
};
