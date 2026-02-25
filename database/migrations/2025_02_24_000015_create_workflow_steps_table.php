<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_order');
            $table->string('name');
            $table->string('role_name'); // Spatie role required to approve
            $table->boolean('is_parallel')->default(false); // With sibling steps in same step_order
            $table->timestamps();
        });

        Schema::table('workflow_steps', fn (Blueprint $table) => $table->index(['workflow_definition_id', 'step_order']));
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
