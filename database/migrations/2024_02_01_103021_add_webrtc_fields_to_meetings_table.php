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
        Schema::table('meetings', function (Blueprint $table) {
            $table->text('offer')->nullable()->after('end_date');
            $table->text('answer')->nullable()->after('offer');
            $table->json('ice_candidates')->nullable()->after('answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('offer');
            $table->dropColumn('answer');
            $table->dropColumn('ice_candidates');
        });
    }
};
