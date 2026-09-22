<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->string('link_drive')->nullable()->after('link_documento');
        });
    }

    public function down(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropColumn('link_drive');
        });
    }
};