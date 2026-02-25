<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sections: Belong to a department. Ministry → Department → Section hierarchy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('sections', fn (Blueprint $table) => $table->index('department_id'));
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
