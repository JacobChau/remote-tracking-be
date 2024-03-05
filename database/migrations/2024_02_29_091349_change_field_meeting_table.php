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
            // remove offer, answer, and candidate
            $table->dropColumn('offer');
            $table->dropColumn('answer');
            $table->dropColumn('ice_candidates');
            $table->dateTime('start_date')->nullable()->change();
            $table->dateTime('end_date')->nullable()->change();
            $table->string('description')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->json('offer')->nullable()->after('end_date');
            $table->json('answer')->nullable()->after('offer');
            $table->json('ice_candidates')->nullable()->after('answer');
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
            $table->dropColumn('description');
        });
    }
};
