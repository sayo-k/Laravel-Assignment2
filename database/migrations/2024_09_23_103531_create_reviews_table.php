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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');  // Student submitting the review
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');  // Student receiving the review
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->text('review_text');
            $table->unsignedInteger('score')->nullable();  // Assigned by the teacher
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
