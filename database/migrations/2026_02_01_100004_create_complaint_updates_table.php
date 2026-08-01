<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();

            // Null when the row was created by the system (e.g. initial submission)
            // rather than by an admin acting through the update form.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // The status the complaint was set to at this point in time.
            $table->string('status');
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_updates');
    }
};
