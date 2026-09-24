<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notificaciones_turnado', function (Blueprint $table) {
            $table->string('message_id')->nullable()->index();
            $table->timestamp('no_entregado_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones_turnado', function (Blueprint $table) {
            $table->dropIndex(['message_id']);
            $table->dropColumn([
                'message_id',
                'no_entregado_en',
            ]);
        });
    }
};