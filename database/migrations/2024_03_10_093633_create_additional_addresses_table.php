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
        Schema::create('additional_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('district')->nullable();
            $table->string('first_line')->nullable();
            $table->string('second_line')->nullable();
            $table->string('third_line')->nullable();
            $table->string('town')->nullable();
            $table->string('post_code')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();
            $table->boolean('status')->default(0); //1 = primary address, 2 = primary billing address
            $table->boolean('type')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_addresses');
    }
};
