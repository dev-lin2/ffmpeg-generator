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
        Schema::create('wish_texts', function (Blueprint $table) {
            $table->id();
            $table->text('wish_1_text_1');
            $table->text('wish_1_text_2');
            $table->text('wish_1_text_3');
            $table->text('wish_2_text_1');
            $table->text('wish_2_text_2');
            $table->text('wish_2_text_3');
            $table->text('wish_3_text_1');
            $table->text('wish_3_text_2');
            $table->text('wish_3_text_3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wish_texts');
    }
};
