<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_definition_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('in_progress'); // in_progress, completed, rejected, cancelled
            $table->unsignedInteger('current_step_order')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('workflow_instances', fn (Blueprint $table) => $table->index(['document_id', 'status']));
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};
