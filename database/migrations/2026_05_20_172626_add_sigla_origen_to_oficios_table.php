<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->string('sigla_origen')
                ->nullable()
                ->after('numero_oficio');
        });
    }

    public function down(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropColumn('sigla_origen');
        });
    }
};