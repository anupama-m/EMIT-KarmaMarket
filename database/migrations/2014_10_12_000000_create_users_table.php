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
    Schema::create('users', function (Blueprint $table) {
        $table->id('user_id');
        $table->string('username', 15);
        $table->integer('points')->default(0);
        $table->string('email', 100)->unique();
        $table->string('phone', 11)->unique();
        $table->string('location', 100);
        $table->string('password', 255);
        $table->enum('occupation', ['student', 'job', 'other']);
        $table->string('institution_name')->nullable();
        $table->string('year')->nullable();
        $table->string('company_name')->nullable();
        $table->json('help_areas');
        $table->boolean('is_volunteer')->default(false);

        // New fields
        $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
        $table->string('role')->default('user'); // You can customize default role as needed

        $table->timestamps(); // Recommended for created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
