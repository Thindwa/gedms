<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->foreignId('workflow_definition_id')->nullable()->after('ministry_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_types', fn (Blueprint $table) => $table->dropConstrainedForeignId('workflow_definition_id'));
    }
};
