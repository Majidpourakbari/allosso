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
            $table->boolean('access_erp')->default(false)->after('password');
            $table->boolean('access_admin_portal')->default(false)->after('access_erp');
            $table->boolean('access_ai_developer')->default(false)->after('access_admin_portal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_erp',
                'access_admin_portal',
                'access_ai_developer',
            ]);
        });
    }
};
