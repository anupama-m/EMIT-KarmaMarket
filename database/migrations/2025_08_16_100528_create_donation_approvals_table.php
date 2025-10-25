<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('donation_approvals', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('donation_id');
        $table->unsignedBigInteger('requester_id'); // user requesting the item
        $table->enum('status', ['pending','accepted','rejected','completed','cancel_requested'])->default('pending');
        $table->boolean('is_confirmed')->default(false);
        $table->timestamps();

        $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
        $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_approvals');
    }
};
