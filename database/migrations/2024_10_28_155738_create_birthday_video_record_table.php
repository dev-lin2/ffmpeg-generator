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
        Schema::create('birthday_video_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('birthday_user_id');
            $table->text('wish_text_1');
            $table->text('wish_text_2');
            $table->text('wish_text_3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_video_record');
    }
};
