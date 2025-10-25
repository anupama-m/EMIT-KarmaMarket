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
    Schema::create('help_post_approvals', function (Blueprint $table) {
        $table->id('approval_id');
        $table->unsignedBigInteger('post_id');
        $table->unsignedBigInteger('helper_id'); // the one who clicked 'I'll help'
        $table->boolean('is_confirmed')->default(false);
        $table->enum('status', ['completed','pending', 'accepted', 'rejected'])->default('pending');
        $table->timestamps();

        $table->foreign('post_id')->references('post_id')->on('help_posts')->onDelete('cascade');
        $table->foreign('helper_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_post_approvals');
    }
};
