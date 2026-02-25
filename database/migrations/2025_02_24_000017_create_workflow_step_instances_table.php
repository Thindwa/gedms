<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_step_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_step_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::table('workflow_step_instances', fn (Blueprint $table) => $table->index(['workflow_instance_id', 'status']));
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_instances');
    }
};
