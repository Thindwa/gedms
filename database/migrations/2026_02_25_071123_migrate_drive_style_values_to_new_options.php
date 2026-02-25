<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departments')
            ->where('drive_style', 'default')
            ->update(['drive_style' => 'drive']);

        DB::table('departments')
            ->where('drive_style', 'classic')
            ->update(['drive_style' => 'sharepoint']);

        DB::table('departments')
            ->where('drive_style', 'compact')
            ->update(['drive_style' => 'drive']);

        DB::table('departments')
            ->where('drive_style', 'card')
            ->update(['drive_style' => 'dropbox']);
    }

    public function down(): void
    {
        // No-op: revert would require knowing original values
    }
};
