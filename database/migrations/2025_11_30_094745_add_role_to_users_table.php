<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds role column to users table for RBAC.
     * CRITICAL: Only 3 roles allowed - admin, officer, member
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role column with ENUM constraint - ONLY admin, officer, member allowed
            $table->enum('role', ['admin', 'officer', 'member'])->default('member')->after('email');
            
            // Additional profile fields
            $table->string('phone', 20)->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_photo')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'profile_photo']);
        });
    }
};
