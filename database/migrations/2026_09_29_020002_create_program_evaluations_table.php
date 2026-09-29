<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->string('evaluation_period'); // TRIWULAN_1, TRIWULAN_2, TRIWULAN_3, TRIWULAN_4, MIDTERM, FINAL, MONTHLY
            $table->date('evaluation_date');
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->decimal('budget_realization', 15, 2)->default(0);
            $table->text('achievements')->nullable();
            $table->text('obstacles')->nullable();
            $table->text('recommendations')->nullable();
            $table->unsignedTinyInteger('score')->default(0); // 0-100
            $table->string('status')->default('DRAFT'); // DRAFT, SUBMITTED, REVIEWED, APPROVED
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_evaluations');
    }
};
