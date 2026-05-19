<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_archivos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('oficio_id')
                ->constrained('oficios')
                ->cascadeOnDelete();

            $table->string('nombre_original');
            $table->string('tipo_archivo');

            $table->boolean('es_sensible')->default(false);

            $table->timestamps();

            $table->index('oficio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_archivos');
    }
};