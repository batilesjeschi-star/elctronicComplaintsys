<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();

            // Path relative to the "public" storage disk, e.g. complaints/12/abc123.jpg
            $table->string('image_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_images');
    }
};
