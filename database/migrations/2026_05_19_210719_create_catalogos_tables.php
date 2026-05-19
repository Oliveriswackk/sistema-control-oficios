<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_oficio', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
        });

        Schema::create('estados_oficio', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->string('color');
            $table->integer('orden');
            $table->boolean('es_final')->default(false);
            $table->boolean('activo')->default(true);
        });

        Schema::create('estados_turnado', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
        });

        Schema::create('tipos_relacion', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('direccional')->default(true);
            $table->boolean('activo')->default(true);
        });

        Schema::create('tipos_participacion', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('implica_responsabilidad')->default(false);
            $table->boolean('activo')->default(true);
        });

        Schema::create('coordinaciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coordinaciones');
        Schema::dropIfExists('tipos_participacion');
        Schema::dropIfExists('tipos_relacion');
        Schema::dropIfExists('estados_turnado');
        Schema::dropIfExists('estados_oficio');
        Schema::dropIfExists('tipos_oficio');
    }
};