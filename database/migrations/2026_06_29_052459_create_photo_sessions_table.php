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
        Schema::create('photo_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->date('shoot_date');
            $table->string('drive_folder_url', 500);
            $table->string('drive_folder_id', 255);
            $table->integer('max_selections')->default(30);
            $table->enum('status', ['active', 'completed', 'delivered'])->default('active');
            $table->string('download_link', 500)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_sessions');
    }
};
