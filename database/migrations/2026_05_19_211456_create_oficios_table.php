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

            /*
            |--------------------------------------------------------------------------
            | Datos generales del oficio
            |--------------------------------------------------------------------------
            */

            $table->string('numero_oficio')->nullable();
            $table->string('consecutivo')->nullable();

            $table->foreignId('tipo_oficio_id')
                ->constrained('tipos_oficio');

            $table->foreignId('estado_id')
                ->constrained('estados_oficio');

            $table->string('asunto');

            $table->text('descripcion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fechas
            |--------------------------------------------------------------------------
            */

            $table->date('fecha_oficio');

            $table->date('fecha_recepcion');

            $table->date('fecha_limite')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Configuración y banderas
            |--------------------------------------------------------------------------
            */

            $table->boolean('requiere_respuesta')->default(false);

            $table->boolean('es_sensible')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Relación documental
            |--------------------------------------------------------------------------
            */

            $table->foreignId('respuesta_a_oficio_id')
                ->nullable()
                ->constrained('oficios')
                ->nullOnDelete()
                ->index(); 
            /*
            |--------------------------------------------------------------------------
            | Remitente
            |--------------------------------------------------------------------------
            */

            $table->string('remitente_nombre')->nullable();

            $table->string('remitente_cargo')->nullable();

            $table->string('remitente_dependencia')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Destinatario
            |--------------------------------------------------------------------------
            */

            $table->string('destinatario_nombre')->nullable();

            $table->string('destinatario_cargo')->nullable();

            $table->string('destinatario_dependencia')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quién elabora
            |--------------------------------------------------------------------------
            */

            $table->string('quien_elabora_nombre')->nullable();

            $table->string('quien_elabora_cargo')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Usuarios y coordinación interna
            |--------------------------------------------------------------------------
            */

            $table->foreignId('responsable_inicial_id')
                ->nullable()
                ->constrained('users');

            $table->foreignId('coordinacion_origen_id')
                ->nullable()
                ->constrained('coordinaciones')
                ->nullOnDelete();

            $table->foreignId('usuario_registro_id')
                ->constrained('users');

            /*
            |--------------------------------------------------------------------------
            | Referencias externas
            |--------------------------------------------------------------------------
            */

            $table->string('link_documento')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estados del flujo
            |--------------------------------------------------------------------------
            */

            $table->timestamp('respondido_en')->nullable();

            $table->timestamp('cerrado_en')->nullable();

            $table->timestamp('cancelado_en')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

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