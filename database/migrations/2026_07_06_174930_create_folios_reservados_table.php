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
        Schema::create('folios_reservados', function (Blueprint $table) {

            $table->id();

            $table->foreignId('coordinacion_id')
                ->constrained('coordinaciones');

            $table->unsignedInteger('anio');

            $table->unsignedInteger('numero');

            $table->string('numero_oficio')->nullable();

            $table->enum('estado', ['reservado', 'usado', 'cancelado'])
                ->default('reservado');

            $table->uuid('grupo_uuid')->nullable();

            $table->foreignId('usuario_reserva_id')
                ->constrained('users');

            $table->text('motivo_cancelacion')->nullable();

            $table->timestamps();

            $table->unique(
                ['coordinacion_id', 'anio', 'numero'],
                'folio_reservado_unique'
            );
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folios_reservados');
    }
};
