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
        Schema::create('notificaciones_turnado', function (Blueprint $table) {
            $table->id();

            $table->foreignId('turnado_id')
                ->unique()
                ->constrained('turnados')
                ->cascadeOnDelete();

            $table->string('destinatario_email');

            $table->string('estado');

            $table->unsignedInteger('intentos')->default(0);

            $table->timestamp('ultimo_intento_en')->nullable();

            $table->timestamp('enviado_en')->nullable();

            $table->timestamp('notificado_manualmente_en')->nullable();

            $table->foreignId('notificado_manualmente_por_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('ultimo_error')->nullable();

            $table->timestamps();

            $table->index('estado');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_turnado');
    }
};
