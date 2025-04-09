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
        Schema::create('service_booking_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_booking_id')->nullable();
            $table->foreign('service_booking_id')->references('id')->on('service_bookings')->onDelete('cascade');
            $table->tinyInteger('review_star')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_booking_reviews');
    }
};
