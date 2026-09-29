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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->after('password');
            $table->string('avatar')->nullable()->after('status');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('position')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('position');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['status', 'avatar', 'phone', 'position', 'last_login_at']);
        });
    }
};
