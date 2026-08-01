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

            // Public tracking code shown on receipts, e.g. ECS-20260725-4F2K9.
            // Generated automatically in the Complaint model, see booted().
            $table->string('reference_number')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            // Kept as a plain string (validated in the Form Request) rather than
            // a database ENUM, so new categories can be added without a migration.
            $table->string('category');

            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('status')->default('pending');
            $table->text('admin_remarks')->nullable();

            // Free-text name of the staff member/team handling this report.
            $table->string('assigned_to')->nullable();

            // Structured link to a department, used for filtering and reports.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            // Photo the admin uploads as proof the issue was fixed.
            $table->string('resolution_photo')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
