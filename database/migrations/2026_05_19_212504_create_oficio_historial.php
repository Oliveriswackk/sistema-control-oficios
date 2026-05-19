<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_historial', function (Blueprint $table) {
            $table->id();

            $table->foreignId('oficio_id')
                ->constrained('oficios')
                ->cascadeOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users');

            $table->string('accion');
            $table->text('descripcion')->nullable();

            $table->foreignId('estado_anterior_id')
                ->nullable()
                ->constrained('estados_oficio');

            $table->foreignId('estado_nuevo_id')
                ->nullable()
                ->constrained('estados_oficio');

            $table->string('entidad_relacionada')->nullable();
            $table->unsignedBigInteger('entidad_relacionada_id')->nullable();

            $table->timestamps();

            $table->index('oficio_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_historial');
    }
};