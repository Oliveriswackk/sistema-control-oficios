<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consecutivos_oficio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('coordinacion_id')
                ->constrained('coordinaciones');

            $table->unsignedInteger('anio');
            $table->unsignedInteger('ultimo_numero');

            $table->timestamps();

            $table->unique(['coordinacion_id', 'anio'], 'consecutivo_coord_anio_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consecutivos_oficio');
    }
};