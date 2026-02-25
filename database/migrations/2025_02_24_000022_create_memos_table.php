<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Memos: Upward (to superiors), Downward (to subordinates), Personal.
 * Optional workflow: requires_approval toggles approval flow.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('direction', 20); // upward, downward, personal
            $table->string('title');
            $table->text('body')->nullable();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ministry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('file_id')->nullable()->constrained()->nullOnDelete(); // optional attachment
            $table->boolean('requires_approval')->default(false);
            $table->string('status', 30)->default('draft'); // draft, sent, acknowledged, approved
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->index(['from_user_id', 'direction', 'status']);
            $table->index(['to_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
