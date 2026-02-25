<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users belong to a department and ministry for org structure and data isolation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ministry_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('ministry_id')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('remember_token');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['ministry_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ministry_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['ministry_id', 'department_id', 'is_active']);
        });
    }
};
