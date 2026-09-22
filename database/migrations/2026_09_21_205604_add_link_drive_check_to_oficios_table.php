<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE oficios
            ADD CONSTRAINT oficios_link_drive_recibidos_check
            CHECK (
                tipo_oficio_id NOT IN (2, 3)
                OR (
                    link_drive IS NOT NULL
                    AND TRIM(link_drive) <> ''
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE oficios
            DROP CONSTRAINT oficios_link_drive_recibidos_check
        ");
    }
};