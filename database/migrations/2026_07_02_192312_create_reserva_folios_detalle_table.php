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
        Schema::create('reserva_folios_detalle', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con el lote
            |--------------------------------------------------------------------------
            */

            $table->foreignId('reserva_folio_id')
                ->constrained('reserva_folios')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identidad del folio
            |--------------------------------------------------------------------------
            */

            $table->foreignId('coordinacion_id')
                ->constrained('coordinaciones');

            $table->unsignedSmallInteger('anio');

            $table->unsignedInteger('consecutivo');

            /*
            |--------------------------------------------------------------------------
            | Estado del folio
            |--------------------------------------------------------------------------
            */

            $table->enum('estado', [
                'pendiente',
                'usado',
                'cancelado',
            ])->default('pendiente');

            /*
            |--------------------------------------------------------------------------
            | Relación con el oficio
            |--------------------------------------------------------------------------
            */

            $table->foreignId('oficio_id')
                ->nullable()
                ->constrained('oficios')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->timestamp('usado_en')->nullable();

            $table->timestamp('cancelado_en')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Integridad
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'coordinacion_id',
                'anio',
                'consecutivo'
            ]);

            $table->index([
                'estado',
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
        Schema::dropIfExists('reserva_folios_detalle');
    }
};