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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('title_english')->nullable();
            $table->string('title_romanian')->nullable();
            $table->longText('des_english')->nullable();
            $table->longText('des_romanian')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(1); //1 = active, 2 = inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
