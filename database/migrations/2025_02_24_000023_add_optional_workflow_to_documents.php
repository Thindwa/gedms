<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional workflow: documents can bypass approval if requires_workflow is false.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('requires_workflow')->default(true)->after('status');
            $table->foreignId('unit_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['requires_workflow', 'unit_id']);
        });
    }
};
