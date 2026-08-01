<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration ADDS to the users table that Breeze already creates.
 * We keep it separate from Breeze's own migration so we never have to
 * hand-edit a file that Laravel/Breeze generated for us.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only two roles exist in this system, so a simple enum is fine here.
            $table->enum('role', ['resident', 'admin'])->default('resident')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address']);
        });
    }
};
