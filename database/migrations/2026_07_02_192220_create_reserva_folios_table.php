<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reserva_folios', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Información del lote
            |--------------------------------------------------------------------------
            */

            $table->foreignId('coordinacion_id')
                ->constrained('coordinaciones');

            $table->unsignedSmallInteger('anio');

            $table->unsignedInteger('cantidad');

            /*
            |--------------------------------------------------------------------------
            | Estado del lote
            |--------------------------------------------------------------------------
            */

            $table->enum('estado', [
                'activo',
                'agotado',
                'cancelado',
            ])->default('activo');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->foreignId('usuario_registro_id')
                ->constrained('users');

            $table->timestamp('cancelado_en')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index([
                'coordinacion_id',
                'anio'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_folios');
    }
};