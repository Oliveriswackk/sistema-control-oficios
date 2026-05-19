<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('oficio_id')->constrained('oficios')->cascadeOnDelete();

            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('coordinacion_id')->constrained('coordinaciones');

            $table->foreignId('tipo_participacion_id')->constrained('tipos_participacion');
            $table->foreignId('estado_turnado_id')->constrained('estados_turnado');

            $table->foreignId('turnado_por_id')->constrained('users');

            $table->timestamp('turnado_en');

            $table->timestamp('atendido_en')->nullable();
            $table->timestamp('cerrado_en')->nullable();

            $table->boolean('es_principal')->default(false);

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('oficio_id');
            $table->index('usuario_id');
            $table->index('estado_turnado_id');
            $table->index('coordinacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnados');
    }
};
