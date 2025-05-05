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
        Schema::table('service_bookings', function (Blueprint $table) {
          $table->unsignedBigInteger('billing_address_id')->nullable()->after('additional_address_id');
          $table->foreign('billing_address_id')->references('id')->on('additional_addresses')->onDelete('cascade');
  
          $table->unsignedBigInteger('shipping_address_id')->nullable()->after('billing_address_id');
          $table->foreign('shipping_address_id')->references('id')->on('additional_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
          $table->dropForeign(['billing_address_id']);
          $table->dropColumn('billing_address_id');
  
          $table->dropForeign(['shipping_address_id']);
          $table->dropColumn('shipping_address_id');
        });
    }
};
