<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'rol_id']);
            $table->index('usuario_id');
        });

        Schema::create('usuario_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'permiso_id']);
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_permisos');
        Schema::dropIfExists('usuario_roles');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};