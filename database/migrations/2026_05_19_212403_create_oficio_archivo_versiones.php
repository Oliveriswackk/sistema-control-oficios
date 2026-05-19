<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_archivo_versiones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('oficio_archivo_id')
                ->constrained('oficio_archivos')
                ->cascadeOnDelete();

            $table->string('ruta');
            $table->string('mime_type');
            $table->unsignedBigInteger('tamano');

            $table->string('hash_sha256');

            $table->unsignedInteger('version');

            $table->boolean('es_actual')->default(false);

            $table->foreignId('subido_por_id')
                ->constrained('users');

            $table->text('motivo_reemplazo')->nullable();

            $table->foreignId('version_anterior_id')
                ->nullable()
                ->constrained('oficio_archivo_versiones');

            $table->timestamps();

            $table->index('oficio_archivo_id');
            $table->index('es_actual');
            $table->index('hash_sha256');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_archivo_versiones');
    }
};