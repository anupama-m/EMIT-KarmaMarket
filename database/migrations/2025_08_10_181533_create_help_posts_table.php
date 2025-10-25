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
        Schema::create('help_posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('post_category');
            $table->string('post_title');
            $table->text('post_description');
            $table->text('post_location');
            $table->integer('points')->default(10);
            $table->timestamp('post_creation_time')->useCurrent();
            $table->string('blood_group')->nullable();
            $table->string('hospital_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_posts');
    }
};
