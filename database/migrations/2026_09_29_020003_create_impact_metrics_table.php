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
        Schema::create('impact_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->string('category'); // STARTUP_GROWTH, PATENT_HKI, COMMERCIALIZATION, WORKFORCE, FUNDING_INVESTMENT, SOCIO_ECONOMIC
            $table->string('metric_name');
            $table->decimal('target_value', 15, 2)->default(0);
            $table->decimal('realized_value', 15, 2)->default(0);
            $table->string('unit'); // Unit Startup, Paten, Rupiah, Orang, Mitra, dll.
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impact_metrics');
    }
};
