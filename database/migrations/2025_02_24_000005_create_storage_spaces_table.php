<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Logical storage spaces: Personal (per user), Department, Ministry.
 * Each space has a single owner; access is derived from type and ownership.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_spaces', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type', 20); // personal, department, ministry
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('owner_ministry_id')->nullable()->constrained('ministries')->nullOnDelete();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('storage_spaces', function (Blueprint $table) {
            $table->index(['type', 'owner_user_id']);
            $table->index(['type', 'owner_department_id']);
            $table->index(['type', 'owner_ministry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_spaces');
    }
};
