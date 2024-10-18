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
        Schema::create('review_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id'); // Foreign key to the reviews table
            $table->unsignedBigInteger('reviewee_id'); // The student being reviewed (receiving the rating for their review)
            $table->unsignedBigInteger('reviewer_id'); // The student giving the review (judging the review)
            $table->integer('clarity_rating')->nullable(); // 1 to 5 for clarity
            $table->integer('constructiveness_rating')->nullable(); // 1 to 5 for constructiveness
            $table->integer('specificity_rating')->nullable(); // 1 to 5 for specificity
            $table->integer('tone_rating')->nullable(); // 1 to 5 for tone
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_ratings');
    }
};
