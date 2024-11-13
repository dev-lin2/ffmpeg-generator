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
            // add uniqid which is unique for each user , 50 characters long
            $table->string('uniqid', 50)->after('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birthday_users', function (Blueprint $table) {
            $table->dropColumn('uniqid');
        });
    }
};
