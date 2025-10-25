<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('donations', function (Blueprint $table) {
        $table->id(); // donation_id
        $table->unsignedBigInteger('user_id'); // FK to users
        $table->string('donation_title');
        $table->string('donation_category');
        $table->text('location');
        $table->decimal('latitude', 10, 7)->nullable();  // latitude (up to ~1cm precision)
        $table->decimal('longitude', 10, 7)->nullable(); // longitude (up to ~1cm precision)
        $table->text('donation_description');
        $table->json('donation_images')->nullable(); // to store multiple image paths
        $table->integer('points')->default(10);
        $table->enum('status', ['open', 'in-progress', 'completed'])->default('open');

        $table->timestamps(); // created_at and updated_at

        // Foreign key constraint
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
