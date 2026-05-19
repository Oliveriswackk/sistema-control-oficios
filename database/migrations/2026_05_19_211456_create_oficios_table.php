<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('numero_oficio')->nullable();

            $table->foreignId('tipo_oficio_id')->constrained('tipos_oficio');
            $table->foreignId('estado_id')->constrained('estados_oficio');

            $table->string('asunto');
            $table->text('descripcion')->nullable();

            $table->date('fecha_oficio');
            $table->date('fecha_recepcion');
            $table->date('fecha_limite')->nullable();

            $table->boolean('requiere_respuesta')->default(false);
            $table->boolean('es_sensible')->default(false);

            $table->foreignId('destinatario_principal_id')->nullable();
            $table->foreignId('coordinacion_origen_id')->constrained('coordinaciones');
            $table->foreignId('usuario_registro_id')->constrained('users');

            $table->timestamp('respondido_en')->nullable();
            $table->timestamp('cerrado_en')->nullable();
            $table->timestamp('cancelado_en')->nullable();

            $table->timestamps();

            $table->unique(['tipo_oficio_id', 'numero_oficio']);
            $table->index('numero_oficio');
            $table->index('estado_id');
            $table->index('fecha_recepcion');
            $table->index('fecha_limite');
            $table->index('coordinacion_origen_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
};