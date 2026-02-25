<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retention_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('retention_years');
            $table->string('action', 20)->default('archive'); // archive, dispose
            $table->boolean('disposal_requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('legal_hold')->default(false)->after('current_version');
        });
    }

    public function down(): void
    {
        Schema::table('documents', fn (Blueprint $table) => $table->dropColumn('legal_hold'));
        Schema::dropIfExists('retention_rules');
    }
};
