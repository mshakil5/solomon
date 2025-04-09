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
        Schema::table('services', function (Blueprint $table) {
          $table->decimal('price', 10, 2)->nullable();
          $table->unsignedBigInteger('type_id')->nullable();
          $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
          $table->longText('information')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            //
        });
    }
};
