<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_relaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('oficio_origen_id')
                ->constrained('oficios')
                ->cascadeOnDelete();

            $table->foreignId('oficio_relacionado_id')
                ->constrained('oficios')
                ->cascadeOnDelete();

            $table->foreignId('tipo_relacion_id')
                ->constrained('tipos_relacion');

            $table->foreignId('usuario_registro_id')
                ->constrained('users');

            $table->timestamps();

            $table->index('oficio_origen_id');
            $table->index('oficio_relacionado_id');
            $table->index('tipo_relacion_id');

            $table->unique(
                ['oficio_origen_id', 'oficio_relacionado_id', 'tipo_relacion_id'],
                'oficio_rel_relacion_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_relaciones');
    }
};