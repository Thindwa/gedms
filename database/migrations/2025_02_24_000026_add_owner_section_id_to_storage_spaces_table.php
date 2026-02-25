<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Section storage space support.
 * Section: all members of that section can access.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('storage_spaces', function (Blueprint $table) {
            $table->foreignId('owner_section_id')->nullable()->after('owner_department_id')->constrained('sections')->nullOnDelete();
        });

        Schema::table('storage_spaces', function (Blueprint $table) {
            $table->index(['type', 'owner_section_id']);
        });
    }

    public function down(): void
    {
        Schema::table('storage_spaces', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_section_id');
        });
    }
};
