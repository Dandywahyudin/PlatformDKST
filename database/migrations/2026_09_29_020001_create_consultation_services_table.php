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
        Schema::create('consultation_services', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->index();
            $table->string('service_type'); // VALUASI_TEKNOLOGI, FASILITASI_HKI, INKUBASI_STARTUP, HILIRISASI_INDUSTRI, LEGALITAS_KONTRAK, LAINNYA
            $table->string('title');
            $table->text('description');
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->string('institution')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('consultant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('meeting_link_or_location')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, IN_REVIEW, SCHEDULED, COMPLETED, REJECTED, CANCELLED
            $table->text('consultation_notes')->nullable();
            $table->text('action_plan')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_services');
    }
};
