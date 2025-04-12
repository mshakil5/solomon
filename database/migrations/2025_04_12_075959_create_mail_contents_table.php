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
        Schema::create('mail_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mail_content_type_id')->nullable();
            $table->foreign('mail_content_type_id')->references('id')->on('mail_content_types')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_contents');
    }
};
