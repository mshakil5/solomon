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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->after('user_id');
            $table->foreign('booking_id')->references('id')->on('service_bookings')->onDelete('set null');
            $table->string('payment_type')->nullable()->after('tranid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
            $table->dropColumn('payment_type');
        });
    }
};
