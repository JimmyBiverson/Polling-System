<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vote_submissions', function (Blueprint $table) {
            $table->foreignId('presiding_officer_id')
                ->nullable()
                ->after('user_id')
                ->constrained('presiding_officers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vote_submissions', function (Blueprint $table) {
            $table->dropForeign(['presiding_officer_id']);
            $table->dropColumn('presiding_officer_id');
        });
    }
};
