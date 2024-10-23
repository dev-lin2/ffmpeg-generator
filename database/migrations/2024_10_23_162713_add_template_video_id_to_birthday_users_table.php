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
        Schema::table('birthday_users', function (Blueprint $table) {
            // Add template_video_id column
            $table->unsignedBigInteger('template_video_id')->nullable()->after('birthday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birthday_users', function (Blueprint $table) {
            $table->dropColumn('template_video_id');
        });
    }
};
