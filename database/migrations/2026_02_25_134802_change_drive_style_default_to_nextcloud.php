<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE departments MODIFY drive_style VARCHAR(20) DEFAULT 'nextcloud'");
        }
        // SQLite: column default can't be changed easily; app uses 'nextcloud' when null
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE departments MODIFY drive_style VARCHAR(20) DEFAULT 'drive'");
        }
    }
};
