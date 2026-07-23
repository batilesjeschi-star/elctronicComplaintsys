<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // Unique human-readable reference number shown on receipts, e.g. ECS-2026-000123
            $table->string('reference_number')->unique();

            // The resident who filed the complaint
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            // Road, Garbage, Drainage, Street Light, Safety, Others
            $table->string('category');

            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Pending, Under Review, In Progress, Resolved, Rejected
            $table->string('status')->default('Pending');

            $table->text('admin_remarks')->nullable();

            // Free-text name of the person assigned (in addition to / instead of a department)
            $table->string('assigned_to')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();

            // Photo uploaded by admin/staff once the issue has been fixed
            $table->string('resolution_photo')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
