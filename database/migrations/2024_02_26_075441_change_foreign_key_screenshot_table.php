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
        Schema::table('screenshots', function (Blueprint $table) {
            $table->dropForeign(['user_meeting_id']);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('meeting_id')->constrained('meetings');
            $table->dropColumn('user_meeting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screenshots', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['meeting_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('meeting_id');
            $table->foreignId('user_meeting_id')->constrained('user_meetings');
        });
    }
};
